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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use IanM\OAuthLine\Exception\InvalidTokenException;
use IanM\OAuthLine\Exception\LineIdentityProviderException;
use Illuminate\Support\Arr;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

class LineProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $openid_configuration;

    /**
     * @var string
     */
    protected $nonce;

    /**
     * Returns the base URL for authorizing a client.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://access.line.me/oauth2/v2.1/authorize';
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://api.line.me/oauth2/v2.1/token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://api.line.me/v2/profile';
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['profile', 'openid', 'email'];
    }

    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Returns authorization parameters based on provided options.
     *
     * @param array $options
     *
     * @return array Authorization parameters
     */
    protected function getAuthorizationParameters(array $options)
    {
        // nonce
        if (empty($options['nonce'])) {
            $options['nonce'] = $this->getRandomState();
        }

        if (is_array($options['scope'])) {
            $separator = $this->getScopeSeparator();
            $options['scope'] = implode($separator, $options['scope']);
        }

        // Store the nonce as it may need to be accessed later on.
        $this->nonce = $options['nonce'];

        // 親クラスのパラメータを追加
        $options = parent::getAuthorizationParameters($options);

        return $options;
    }

    /**
     * Checks a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param array|string      $data     Parsed response data
     *
     * @throws IdentityProviderException
     *
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw LineIdentityProviderException::clientException($response, $data);
        } elseif (isset($data['error'])) {
            throw LineIdentityProviderException::oauthException($response, $data);
        }
    }

    /**
     * Requests resource owner details.
     *
     * @param AccessToken $token
     *
     * @return mixed
     */
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        $url = $this->getResourceOwnerDetailsUrl($token);

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        $response = $this->getParsedResponse($request);

        if (false === is_array($response)) {
            throw new UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }

        if (!isset($response['email'])) {
            $response['email'] = $this->getEmail($token->getValues()['id_token'], $this->nonce);
        }

        return $response;
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param array       $response
     * @param AccessToken $token
     *
     * @return LineResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new LineResourceOwner($response);
    }

    /**
     * Returns the current value of the nonce parameter.
     *
     * This can be accessed by the redirect handler during authorization.
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Get user email.
     *
     * @param string $jwt   ID Token(JWT)
     * @param string $nonce
     *
     * @return string|null
     */
    public function getEmail($jwt, $nonce)
    {
        $url = 'https://api.line.me/oauth2/v2.1/verify';

        $client = new Client();

        try {
            $response = $client->post($url, [
                'form_params' => [
                    'id_token'  => $jwt,
                    'client_id' => $this->clientId,
                    'nonce'     => $nonce,
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);
        } catch (ClientException $e) {
            $contents = $e->getResponse()->getBody()->getContents();

            throw new InvalidTokenException($this->parseJson($contents)['error_description']);
        }

        $parsed_response = $this->parseResponse($response);

        $email = Arr::get($parsed_response, 'email');

        return $email;
    }
}
