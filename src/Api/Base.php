<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Session\Session;
use Psr\Http\Message\ResponseInterface;

abstract class Base
{
    /**
     * @var string
     */
    protected const API_ENDPOINT = '/';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Concur constructor.
     * @param Client $client
     * @param Session $session
     */
    public function __construct(Client $client, Session $session)
    {
        $this->client = $client;
        $this->session = $session;
        $this->config = config('services.concur');
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function request(string $url, string $method = 'GET', array $options = [])
    {
        return $this->client->request($method, $url, $options);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function url(array $params = []): string
    {
        $url = $this->session->get('geolocation') . static::API_ENDPOINT;

        return empty($params) ? $url : $url . http_build_query($params);
    }
}
