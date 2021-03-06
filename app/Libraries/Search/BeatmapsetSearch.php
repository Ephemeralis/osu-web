<?php

/**
 *    Copyright (c) ppy Pty Ltd <contact@ppy.sh>.
 *
 *    This file is part of osu!web. osu!web is distributed with the hope of
 *    attracting more community contributions to the core ecosystem of osu!.
 *
 *    osu!web is free software: you can redistribute it and/or modify
 *    it under the terms of the Affero GNU General Public License version 3
 *    as published by the Free Software Foundation.
 *
 *    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
 *    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *    See the GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Libraries\Search;

use App\Libraries\Elasticsearch\BoolQuery;
use App\Libraries\Elasticsearch\QueryHelper;
use App\Libraries\Elasticsearch\RecordSearch;
use App\Models\Beatmap;
use App\Models\Beatmapset;
use App\Models\Score;

class BeatmapsetSearch extends RecordSearch
{
    public $recommendedDifficulty;

    /**
     * @param BeatmapsetSearchParams $params
     */
    public function __construct(?BeatmapsetSearchParams $params = null)
    {
        parent::__construct(
            Beatmapset::esIndexName(),
            $params ?? new BeatmapsetSearchParams,
            Beatmapset::class
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        static $partialMatchFields = ['artist', 'artist.*', 'artist_unicode', 'creator', 'title', 'title.raw', 'title.*', 'title_unicode', 'tags^0.5'];

        $query = (new BoolQuery());

        if (present($this->params->queryString)) {
            $terms = explode(' ', $this->params->queryString);

            // the subscoping is not necessary but prevents unintentional accidents when combining other matchers
            $query->must(
                (new BoolQuery)
                    // results must contain at least one of the terms and boosted by containing all of them,
                    // or match the id of the beatmapset.
                    ->shouldMatch(1)
                    ->should(['term' => ['_id' => ['value' => $this->params->queryString, 'boost' => 100]]])
                    ->should(QueryHelper::queryString($this->params->queryString, $partialMatchFields, 'or', 1 / count($terms)))
                    ->should(QueryHelper::queryString($this->params->queryString, [], 'and'))
            );
        }

        $this->addBlacklistFilter($query);
        $this->addModeFilter($query);
        $this->addRecommendedFilter($query);
        $this->addGenreFilter($query);
        $this->addLanguageFilter($query);
        $this->addExtraFilter($query);
        $this->addRankFilter($query);
        $this->addStatusFilter($query);
        $this->addPlayedFilter($query);

        return $query;
    }

    public function records()
    {
        return $this->response()->records()->with('beatmaps')->get();
    }

    private function addBlacklistFilter($query)
    {
        static $fields = ['artist', 'source', 'tags'];
        $bool = new BoolQuery;

        foreach ($fields as $field) {
            $bool->mustNot([
                'terms' => [
                    $field => [
                        'index' => config('osu.elasticsearch.prefix').'blacklist',
                        'type' => 'blacklist', // FIXME: change to _doc after upgrading from 6.1
                        'id' => 'beatmapsets',
                        // can be changed to per-field blacklist as different fields should probably have different restrictions.
                        'path' => 'keywords',
                    ],
                ],
            ]);
        }

        $query->filter($bool);
    }

    private function addExtraFilter($query)
    {
        foreach ($this->params->extra as $val) {
            $query->filter(['term' => [$val => true]]);
        }
    }

    private function addGenreFilter($query)
    {
        if ($this->params->genre !== null) {
            $query->filter(['term' => ['genre_id' => $this->params->genre]]);
        }
    }

    private function addLanguageFilter($query)
    {
        if ($this->params->language !== null) {
            $query->filter(['term' => ['language_id' => $this->params->language]]);
        }
    }

    private function addModeFilter($query)
    {
        if ($this->params->mode !== null) {
            $modes = [$this->params->mode];
            if ($this->params->includeConverts && $this->params->mode !== Beatmap::MODES['osu']) {
                $modes[] = Beatmap::MODES['osu'];
            }

            $query->filter(['terms' => ['difficulties.playmode' => $modes]]);
        }
    }

    private function addPlayedFilter($query)
    {
        if ($this->params->playedFilter === 'played') {
            $query->filter(['terms' => ['difficulties.beatmap_id' => $this->getPlayedBeatmapIds()]]);
        } elseif ($this->params->playedFilter === 'unplayed') {
            $query->mustNot(['terms' => ['difficulties.beatmap_id' => $this->getPlayedBeatmapIds()]]);
        }
    }

    private function addRankFilter($query)
    {
        if (empty($this->params->rank)) {
            return;
        }

        $query->filter(['terms' => ['difficulties.beatmap_id' => $this->getPlayedBeatmapIds($this->params->rank)]]);
    }

    private function addRecommendedFilter($query)
    {
        if ($this->params->showRecommended && $this->params->user !== null) {
            // TODO: index convert difficulties and handle them.
            $difficulty = $this->params->getRecommendedDifficulty();
            $query->filter([
                'range' => [
                    'difficulties.difficultyrating' => [
                        'gte' => $difficulty - 0.5,
                        'lte' => $difficulty + 0.5,
                    ],
                ],
            ]);
        }
    }

    // statuses are non scoring for the query context.
    private function addStatusFilter($mainQuery)
    {
        $query = new BoolQuery;

        switch ($this->params->status) {
            case 'any':
                break;
            case 'ranked':
                $query->should([
                    ['match' => ['approved' => Beatmapset::STATES['ranked']]],
                    ['match' => ['approved' => Beatmapset::STATES['approved']]],
                ]);
                break;
            case 'loved':
                $query->must(['match' => ['approved' => Beatmapset::STATES['loved']]]);
                break;
            case 'favourites':
                $favs = model_pluck($this->params->user->favouriteBeatmapsets(), 'beatmapset_id', Beatmapset::class);
                $query->must(['ids' => ['type' => 'beatmaps', 'values' => $favs]]);
                break;
            case 'qualified':
                $query->should([
                    ['match' => ['approved' => Beatmapset::STATES['qualified']]],
                ]);
                break;
            case 'pending':
                $query->should([
                    ['match' => ['approved' => Beatmapset::STATES['wip']]],
                    ['match' => ['approved' => Beatmapset::STATES['pending']]],
                ]);
                break;
            case 'graveyard':
                $query->must(['match' => ['approved' => Beatmapset::STATES['graveyard']]]);
                break;
            case 'mine':
                $maps = model_pluck($this->params->user->beatmapsets(), 'beatmapset_id');
                $query->must(['ids' => ['type' => 'beatmaps', 'values' => $maps]]);
                break;
            default: // null, etc
                $query->should([
                    ['match' => ['approved' => Beatmapset::STATES['ranked']]],
                    ['match' => ['approved' => Beatmapset::STATES['approved']]],
                    ['match' => ['approved' => Beatmapset::STATES['loved']]],
                ]);
            break;
        }

        $mainQuery->filter($query);
    }

    private function getPlayedBeatmapIds(?array $rank = null)
    {
        $unionQuery = null;
        foreach ($this->getSelectedModes() as $mode) {
            $newQuery = Score\Best\Model::getClass($mode)
                ::forUser($this->params->user)
                ->select('beatmap_id');

            if ($rank !== null) {
                $newQuery->whereIn('rank', $rank);
            }

            if ($unionQuery === null) {
                $unionQuery = $newQuery;
            } else {
                $unionQuery->union($newQuery);
            }
        }

        return model_pluck($unionQuery, 'beatmap_id');
    }

    private function getSelectedModes()
    {
        return $this->params->mode === null ? array_values(Beatmap::MODES) : [$this->params->mode];
    }
}
