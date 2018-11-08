<?php

namespace VdPoel\Concur;

class ConcurClient
{
    /**
     * The underlying connection to the Concur SAP APi
     *
     * @var string
     */
    protected $credentials;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $connection;

    /**
     * ConcurClient constructor.
     *
     * @param ConcurCredentials  $credentials
     * @param \GuzzleHttp\Client $connection
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(ConcurCredentials $credentials, $connection)
    {
        $this->credentials = $credentials;
        $this->connection = $connection;

        $this->getToken();
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getToken(): string
    {
        $token = session()->get('concur_api_token');

        if ($token === null) {

            $response = $this->connection->request('POST','oauth2/v0/token', [
                'form_params' => $this->credentials->toArray()
            ]);

            $token = (string) $response->getBody();

            $this->setToken($token);

        }

        return $token;

    }

    /**
     * @return ConcurClient
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function refreshToken(): self
    {
        $token = new ConcurTokenParser(session()->get('concur_api_token'));

        if ($token->canRefresh()) {
            $response = $this->connection->request('POST', 'oauth2/v0/token', [
                'form_params' => [
                    'client_id'     => $this->credentials->getAttribute('client_id'),
                    'grant_type' => $this->credentials->getAttribute('grant_type'),
                    'client_secret' => $this->credentials->getAttribute('client_secret'),
                    'refresh_token' => $token->getAttribute('refresh_token'),
                    'scope'         => $token->getAttribute('scope'),
                ]
            ]);

            $this->setToken((string) $response->getBody());
        }

        return $this;

    }

    /**
     * @param string $token
     *
     * @return self
     */
    public function setToken(string $token): self
    {
        session()->put('concur_api_token', $token);

        return $this;
    }

}
