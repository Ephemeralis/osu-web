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

import * as moment from 'moment';
import * as React from 'react';
import { Sort } from 'sort';
import { UserCards } from 'user-cards';

type Filter = 'all' | 'online' | 'offline';
type SortMode = 'last_visit' | 'username';

const filters: Filter[] = ['all', 'online', 'offline'];
const sortModes: SortMode[] = ['last_visit', 'username'];

interface Props {
  title?: string;
  users: User[];
}

interface State {
  filter: Filter;
  sortMode: SortMode;
}

function usernameSortAscending(x: User, y: User) {
  return x.username.localeCompare(y.username);
}

export class UserList extends React.PureComponent<Props> {
  readonly state: State = {
    filter: this.filterFromUrl,
    sortMode: this.sortFromUrl,
  };

  private get filterFromUrl() {
    const url = new URL(location.href);

    return this.getAllowedQueryStringValue(filters, url.searchParams.get('filter'));
  }

  private get sortedUsers() {
    const users = this.getFilteredUsers(this.state.filter).slice();

    switch (this.state.sortMode) {
      case 'username':
        return users.sort(usernameSortAscending);

      default:
        return users.sort((x, y) => {
          if (x.is_online && y.is_online) {
            return usernameSortAscending(x, y);
          }

          if (x.is_online || y.is_online) {
            return x.is_online ? -1 : 1;
          }

          return moment(y.last_visit || 0).diff(moment(x.last_visit || 0));
        });
    }
  }

  private get sortFromUrl() {
    const url = new URL(location.href);

    return this.getAllowedQueryStringValue(sortModes, url.searchParams.get('user_sort'));
  }

  onSortSelected = (event: React.SyntheticEvent) => {
    const value = (event.currentTarget as HTMLElement).dataset.value;
    const url = osu.updateQueryString(null, { user_sort: value });

    Turbolinks.controller.advanceHistory(url);
    this.setState({ sortMode: value });
  }

  optionSelected = (event: React.SyntheticEvent) => {
    event.preventDefault();
    const key = (event.currentTarget as HTMLElement).dataset.key;
    const url = osu.updateQueryString(null, { filter: key });

    Turbolinks.controller.advanceHistory(url);
    this.setState({ filter: key });
  }

  render(): React.ReactNode {
    return (
      <>
        {this.renderSelections()}
        <div className='user-list'>
          <div className='user-list__toolbar'>
            {this.renderSorter()}
          </div>

          <UserCards users={this.sortedUsers} />
        </div>
      </>
    );
  }

  renderOption(key: string, text: string | number, active = false) {
    // FIXME: change all the names
    const modifiers = active ? ['active'] : [];
    let className = osu.classWithModifiers('update-streams-v2__item', modifiers);
    className += ` t-changelog-stream--${key}`;

    return (
      <a
        className={className}
        data-key={key}
        href={osu.updateQueryString(null, { filter: key })}
        key={key}
        onClick={this.optionSelected}
      >
        <div className='update-streams-v2__bar u-changelog-stream--bg' />
        <p className='update-streams-v2__row update-streams-v2__row--name'>{osu.trans(`users.status.${key}`)}</p>
        <p className='update-streams-v2__row update-streams-v2__row--version'>{text}</p>
      </a>
    );
  }

  renderSelections() {
    return (
      <div className='update-streams-v2 update-streams-v2--with-active update-streams-v2--user-list'>
        <div className='update-streams-v2__container'>
          {
            filters.map((filter) => {
              return this.renderOption(filter, this.getFilteredUsers(filter).length, filter === this.state.filter);
            })
          }
        </div>
      </div>
    );
  }

  renderSorter() {
    return (
      <Sort
        modifiers={['user-list']}
        onSortSelected={this.onSortSelected}
        sortMode={this.state.sortMode}
        values={sortModes}
      />
    );
  }

  private getAllowedQueryStringValue<T>(allowed: T[], value: unknown) {
    const casted = value as T;
    if (allowed.indexOf(casted) > -1) {
      return casted;
    }

    return allowed[0];
  }

  private getFilteredUsers(filter: Filter) {
    // TODO: should be cached or something
    switch (filter) {
      case 'online':
        return this.props.users.filter((user) => user.is_online);
      case 'offline':
        return this.props.users.filter((user) => !user.is_online);
      default:
        return this.props.users;
    }
  }
}
