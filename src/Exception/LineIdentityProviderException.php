<?php

/*
 * This file is part of ianm/oauth-line.
 *
 * Copyright (c) 2022 IanM.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace IanM\OAuthLine\Exception;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class LineIdentityProviderException extends IdentityProviderException
{
    public static function clientException(ResponseInterface $response, array $data)
    {
        return static::fromResponse(
            $response,
            (string) $response->getReasonPhrase()
        );
    }

    public static function oauthException(ResponseInterface $response, array $data)
    {
        if ($data['error_description']) {
            $message = $data['error_description'];
        } else {
            $message = $data['error'];
        }

        return static::fromResponse(
            $response,
            $message
        );
    }

    /**
     * @param ResponseInterface $response
     * @param string|null       $message
     *
     * @return static
     */
    protected static function fromResponse(ResponseInterface $response, $message = null)
    {
        return new static($message, $response->getStatusCode(), (string) $response->getBody());
    }
}
