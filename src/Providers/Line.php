<?php

namespace IanM\OAuthLine\Providers;

use Flarum\Forum\Auth\Registration;
use FoF\OAuth\Provider;
use League\OAuth2\Client\Provider\AbstractProvider;
use GNOffice\OAuth2\Client\Provider\Line as LineProvider;

class Line extends Provider
{
    /**
     * @var ALineProvider
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

    public function suggestions(Registration $registration, $user, string $token)
    {
        $registration
            ->suggestUsername($user->getName())
            ->setPayload($user->toArray());

        $picture = $user->getPicture();

        if ($picture) {
            $registration->provideAvatar($picture . '/large');
        }
    }
}
