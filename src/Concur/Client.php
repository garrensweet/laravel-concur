<?php

namespace Concur;

class Client
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
     * @param Credentials        $credentials
     * @param \GuzzleHttp\Client $connection
     */
    public function __construct(Credentials $credentials, \GuzzleHttp\Client $connection)
    {
        $this->credentials = $credentials;
        $this->connection = $connection;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        if ($this->token === null) {
            $response = $this->connection->post('/oauth2/v0/token', [
                'body' => $this->credentials->toArray()
            ]);

            $this->token = (string) $response->getBody();
        }

        return $this->token;

    }


}
