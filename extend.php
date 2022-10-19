<?php

/*
 * This file is part of ianm/oauth-line.
 *
 * Copyright (c) 2022 IanM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace IanM\OAuthLine;

use Flarum\Extend;
use FoF\OAuth\Extend as OAuthExtend;
use IanM\OAuthLine\Providers\Line;

return [
    (new Extend\Frontend('forum'))
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    new Extend\Locales(__DIR__.'/locale'),

    (new OAuthExtend\RegisterProvider(Line::class)),
];
