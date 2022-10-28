<?php

/*
 * This file is part of ianm/oauth-line.
 *
 * Copyright (c) 2022 IanM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace IanM\OAuthLine\Providers;

use Flarum\Forum\Auth\Registration;
use FoF\OAuth\Provider;
use League\OAuth2\Client\Provider\AbstractProvider;

class Line extends Provider
{
    /**
     * @var LineProvider
     */
    protected $provider;

    public function name(): string
    {
        return 'line';
    }

    public function link(): string
    {
        return 'https://developers.line.biz/console/';
    }

    public function fields(): array
    {
        return [
            'client_id'     => 'required',
            'client_secret' => 'required',
        ];
    }

    public function provider(string $redirectUri): AbstractProvider
    {
        return $this->provider = new LineProvider([
            'clientId'     => $this->getSetting('client_id'),
            'clientSecret' => $this->getSetting('client_secret'),
            'redirectUri'  => $redirectUri,
        ]);
    }

    public function options(): array
    {
        return [
            'scope' => ['profile', 'openid', 'email'],
            'bot_prompt' => 'normal',
        ];
    }

    public function suggestions(Registration $registration, $user, string $token)
    {
        $registration
            ->suggestUsername($user->getName())
            ->setPayload($user->toArray());

        if ($email = $user->getEmail()) {
            $this->verifyEmail($email);
            $registration->provideTrustedEmail($email);
        }

        if ($picture = $user->getPicture()) {
            $registration->provideAvatar($picture.'/large');
        }
    }
}
