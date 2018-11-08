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
     * @var string
     */
    protected $token;

    /**
     * Client constructor.
     *
     * @param ConcurCredentials  $credentials
     * @param \GuzzleHttp\Client $connection
     */
    public function __construct(ConcurCredentials $credentials, \GuzzleHttp\Client $connection)
    {
        $this->credentials = $credentials;
        $this->connection = $connection;
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getToken(): string
    {
        if ($this->token === null) {
            $response = $this->connection->request('POST',config('concur.api_url_prefix').'oauth2/v0/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ],
                'form_params' => $this->credentials->toArray()
            ]);

            $this->token = (string) $response->getBody();
        }

        return $this->token;

    }


}
