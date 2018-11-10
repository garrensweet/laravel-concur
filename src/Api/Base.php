<?php

namespace VdPoel\Concur\Api;

use GoetasWebservices\XML\XSDReader\Exception\IOException;
use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\SchemaReader;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Cache\Repository;
use Psr\Http\Message\ResponseInterface;

abstract class Base
{
    /**
     * @var string
     */
    protected const API_ENDPOINT = '/';

    /**
     * @var ?string
     */
    protected const XSD = null;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Repository
     */
    protected $cache;

    /**
     * @var SchemaReader
     */
    protected $reader;

    /**
     * Concur constructor.
     * @param Client $client
     * @param Repository $cache
     * @param SchemaReader $reader
     */
    public function __construct(Client $client, Repository $cache, SchemaReader $reader)
    {
        $this->client = $client;
        $this->cache  = $cache;
        $this->reader = $reader;
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
        return with($this->session->get('geolocation') . static::API_ENDPOINT, function ($resourceUri) use ($params) {
            return empty($params) ? $resourceUri : $resourceUri . http_build_query($params);
        });
    }

    /**
     * @return Schema|null
     * @throws IOException
     */
    protected function schema()
    {
        return !is_null(static::XSD) ? $this->reader->readFile(static::XSD) : null;
    }
}
