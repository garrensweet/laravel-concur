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
    public function getAccessToken(): bool
    {
        return $this->sendAuthenticationRequest($this->body());
    }

    /**
     * @return bool
     * @throws AuthenticationException
     * @throws GuzzleException
     */
    public function refreshAccessToken(): bool
    {
        return $this->sendAuthenticationRequest($this->body('refresh_token'));
    }

    /**
     * @param array $body
     * @return bool
     * @throws AuthenticationException
     * @throws GuzzleException
     */
    protected function sendAuthenticationRequest(array $body)
    {
        $response = $this->request($this->authorizationUrl(), 'POST', ['form_params' => $body]);

        if ($response->getStatusCode() === 200) {
            $contents = json_decode($response->getBody(), true);

            if (is_array($contents) && json_last_error() === JSON_ERROR_NONE) {
                foreach ($contents as $key => $value) {
                    $this->session->put($key, $value);
                }

                return true;
            }
        }

        throw new AuthenticationException('Concur API authentication failed.');
    }

    /**
     * @return string|null
     */
    protected function authorizationUrl(): ?string
    {
        return data_get($this->config, 'authorization_url');
    }

    /**
     * @param string $type
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function body(string $type = 'password'): array
    {
        $params = [
            'client_id'     => data_get($this->config, 'client_id'),
            'client_secret' => data_get($this->config, 'client_secret'),
            'scope'         => data_get($this->config, 'scope'),
            'grant_type'    => $type
        ];

        switch ($type) {
            case 'password':
                return array_merge($params, [
                    'username' => data_get($this->config, 'username'),
                    'password' => data_get($this->config, 'password'),
                    'credtype' => $type
                ]);
                break;
            case 'refresh_token':
                return array_merge($params, [
                    'refresh_token' => $this->session->get('refresh_token')
                ]);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unsupported grant type: %s', $type), 400);
        }
    }
}
