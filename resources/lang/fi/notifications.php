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

return [
    'all_read' => 'Kaikki ilmoitukset luettu!',
    'mark_all_read' => 'Poista kaikki',
    'message_multi' => '',

    'item' => [
        'beatmapset' => [
            '_' => 'Beatmappi',

            'beatmapset_discussion' => [
                '_' => '',
                'beatmapset_discussion_lock' => '',
                'beatmapset_discussion_post_new' => '',
                'beatmapset_discussion_unlock' => '',
            ],

            'beatmapset_state' => [
                '_' => '',
                'beatmapset_disqualify' => '',
                'beatmapset_love' => '',
                'beatmapset_nominate' => '',
                'beatmapset_qualify' => '',
                'beatmapset_reset_nominations' => '',
            ],
        ],

        'forum_topic' => [
            '_' => 'Foorumin aihe',

            'forum_topic_reply' => [
                '_' => 'Uusi foorumi vastaus',
                'forum_topic_reply' => ':username vastasi foorumi aiheeseen ":title".',
            ],
        ],

        'legacy_pm' => [
            '_' => '',

            'legacy_pm' => [
                '_' => '',
                'legacy_pm' => ':count_delimited lukematon viesti.|:count_delimited lukemattomia viestejä.',
            ],
        ],
    ],
];
