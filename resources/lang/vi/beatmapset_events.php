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
    'event' => [
        'approve' => 'Được Chấp Nhận.',
        'discussion_delete' => 'Moderator đã xóa cuộc thảo luận :discussion.',
        'discussion_lock' => '',
        'discussion_post_delete' => 'Moderator đã xóa bài viết từ cuộc thảo luận :discussion.',
        'discussion_post_restore' => 'Moderator đã phục hồi bài viết từ cuộc thảo luận :discussion.',
        'discussion_restore' => 'Moderator đã phục hồi cuộc thảo luận :discussion.',
        'discussion_unlock' => '',
        'disqualify' => 'Disqualified bởi :user. Reason: :text.',
        'disqualify_legacy' => 'Disqualified bởi :user. Lí do: :text.',
        'issue_reopen' => 'Vấn đề đã giải quyết :discussion được mở lại.',
        'issue_resolve' => 'Vấn đề :discussion đã được giải quyết.',
        'kudosu_allow' => 'Kudosu của cuộc thảo luận :discussion đã không còn bị từ chối nữa.',
        'kudosu_deny' => 'Cuộc thảo luận :discussion đã bị từ chối kudosu.',
        'kudosu_gain' => 'Cuộc thảo luận :discussion bởi :user đã nhận được đủ votes để có kudosu.',
        'kudosu_lost' => 'Cuộc thảo luận :discussion bởi :user đã mất votes và kudosu đã nhận được bị loại bỏ.',
        'kudosu_recalculate' => 'Kudosu của cuộc thảo luận :discussion đã được tính lại.',
        'love' => 'Loved bởi :user',
        'nominate' => 'Được đề cử (nominated) bởi :user.',
        'nomination_reset' => 'Vấn đề mới :discussion đã khiến cho đề cử bị hoàn lại.',
        'qualify' => 'Beatmap này đã đạt được số đề cử (nominations) cần thiết và đã qualified.',
        'rank' => 'Đã được xếp hạng (Ranked).',
    ],

    'index' => [
        'title' => 'Những Sự Kiện Của Beatmapset',

        'form' => [
            'period' => 'Giai đoạn',
            'types' => 'Loại',
        ],
    ],

    'item' => [
        'content' => 'Nội dung',
        'discussion_deleted' => '[đã xóa]',
        'type' => 'Loại',
    ],

    'type' => [
        'approve' => 'Chấp nhận',
        'discussion_delete' => 'Xóa cuộc thảo luận',
        'discussion_post_delete' => 'Xóa trả lời của cuộc thảo luận',
        'discussion_post_restore' => 'Phục hồi trả lời của cuộc thảo luận',
        'discussion_restore' => 'Phục hồi cuộc thảo luận',
        'disqualify' => 'Disqualification',
        'issue_reopen' => 'Mở lại cuộc thảo luận',
        'issue_resolve' => 'Giải quyết cuộc thảo luận',
        'kudosu_allow' => 'Cho phép kudosu',
        'kudosu_deny' => 'Từ chối kudosu',
        'kudosu_gain' => 'Kudosu đạt được',
        'kudosu_lost' => 'Kudosu giảm',
        'kudosu_recalculate' => 'Tính lại kudosu',
        'love' => 'Love',
        'nominate' => 'Đề cử',
        'nomination_reset' => 'Đặt lại đề cử',
        'qualify' => 'Qualification',
        'rank' => 'Xếp hạng',
    ],
];
