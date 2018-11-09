<?php

namespace VdPoel\Concur\Api;

use GoetasWebservices\XML\XSDReader\Exception\IOException;
use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\SchemaReader;
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
     * @var string
     */
    protected const XSD = 'http://localhost';

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
     * @var SchemaReader
     */
    protected $reader;

    /**
     * Concur constructor.
     * @param Client $client
     * @param Session $session
     * @param SchemaReader $reader
     */
    public function __construct(Client $client, Session $session, SchemaReader $reader)
    {
        $this->client  = $client;
        $this->session = $session;
        $this->reader  = $reader;
        $this->config  = config('services.concur');
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

    /**
     * @return Schema
     * @throws IOException
     */
    protected function schema(): Schema
    {
        return $this->reader->readFile(static::XSD);
    }
}
