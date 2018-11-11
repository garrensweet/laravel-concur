<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;

/**
 * Class Authentication
 * @package VdPoel\Concur\Api
 */
class Authentication extends Base
{
    /**
     * @return bool
     * @throws AuthenticationException
     * @throws GuzzleException
     */
    public function login()
    {
        if (!$this->exists('access_token')) {
            return $this->requestAccessToken();
        }

        if ($this->exists('access_token') && $this->expired()) {
            return $this->refreshAccessToken();
        }

        return true;
    }

    /**
     * @return bool
     * @throws AuthenticationException
     * @throws GuzzleException
     */
    protected function requestAccessToken(): bool
    {
        return $this->sendAuthenticationRequest([
            'client_id'     => data_get($this->config, 'api.params.client_id'),
            'client_secret' => data_get($this->config, 'api.params.client_secret'),
            'scope'         => data_get($this->config, 'api.params.scope'),
            'username'      => data_get($this->config, 'api.params.username'),
            'password'      => data_get($this->config, 'api.params.password'),
            'grant_type'    => 'password',
            'credtype'      => 'password'
        ]);
    }

    /**
     * @param array $body
     * @return bool
     * @throws AuthenticationException
     * @throws GuzzleException
     */
    protected function sendAuthenticationRequest(array $body)
    {
        $response = $this->request($this->authorizationUrl(), 'POST', [
            'form_params' => $body,
            'headers'     => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            $this->setCacheData($this->parseResponse($response));

            return true;
        }

        throw new AuthenticationException('Concur API authentication failed.');
    }

    /**
     * @return string|null
     */
    protected function authorizationUrl(): ?string
    {
        return data_get($this->config, 'api.urls.authorization');
    }

    /**
     * @return bool
     * @throws AuthenticationException
     * @throws GuzzleException
     */
    protected function refreshAccessToken(): bool
    {
        return $this->sendAuthenticationRequest([
            'client_id'     => data_get($this->config, 'api.params.client_id'),
            'client_secret' => data_get($this->config, 'api.params.client_secret'),
            'scope'         => data_get($this->config, 'api.params.scope'),
            'refresh_token' => $this->cache->get('refresh_token'),
            'grant_type'    => 'refresh_token'
        ]);
    }

    /**
     * @return string
     */
    protected function tokenType(): string
    {
        return 'OAuth';
    }
}
