<?php

namespace VdPoel\Concur\Api;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Store;
use Psr\Http\Message\ResponseInterface;
use VdPoel\Concur\ErrorHandler;
use VdPoel\Concur\Events\TravelProfile\TravelProfileNotFound;

/**
 * Class Base
 * @package VdPoel\Concur\Api
 */
abstract class Base
{
    /**
     * @var string
     */
    protected const API_ENDPOINT = '/';

    /**
     * @var int
     */
    protected const BATCH_MAX_ITEMS = 500;

    /**
     * @var int
     */
    protected const CACHE_LIFETIME = 5;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Store
     */
    protected $cache;

    /**
     * @var ErrorHandler
     */
    protected $errorHandler;

    /**
     * Concur constructor.
     * @param Client $client
     * @param CacheManager $cache
     */
    public function __construct(Client $client, CacheManager $cache)
    {
        $this->client = $client;
        $this->cache  = $cache->getStore();
        $this->config = config('concur');
        $this->errorHandler = new ErrorHandler();

        if (method_exists($this->cache, 'setPrefix')) {
            $this->cache->setPrefix(implode('.', ['Concur', $this->getCachePrefix()]));
        }
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function parseResponse(ResponseInterface $response)
    {
        switch ($this->getContentType($response->getHeaders())) {
            case 'application/xml;charset=UTF-8':
                return simplexml_load_string($response->getBody()->getContents());
                break;
            case 'application/json;charset=UTF-8':
                $contents = json_decode($response->getBody(), true);

                if (is_array($contents) && json_last_error() === JSON_ERROR_NONE) {
                    return $contents;
                }

                break;
        }

        return null;
    }

    /**
     * @param array $items
     */
    public function setCacheData(array $items = []): void
    {
        foreach ($items as $key => $value) {
            if ($key === 'expires_in') {
                $this->setTokenExpiration($value);
            }

            $this->cache->put($key, $value, static::CACHE_LIFETIME);
        }
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getCachedData(string $key)
    {
        return $this->exists($key) ? $this->cache->get($key) : null;
    }

    /**
     * @return string
     */
    protected function getCachePrefix(): string
    {
        return class_basename(static::class);
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
        return $this->client->request($method, $url, array_merge($this->headers(), $options));
    }

    /**
     * @return array
     */
    protected function headers(): array
    {
        $headers = ['headers' => ['Content-Type' => 'application/xml']];

        if ($token = $this->cache->get('access_token')) {
            data_set($headers, 'headers.Authorization', $this->getAuthorizationHeader($token));
        }

        return $headers;
    }

    /**
     * @param string $token
     * @return string
     */
    protected function getAuthorizationHeader(string $token): string
    {
        return sprintf('%s %s', $this->tokenType(), $token);
    }

    /**
     * @return string
     */
    protected abstract function tokenType(): string;

    /**
     * @param array $headers
     * @return string|null
     */
    protected function getContentType(array $headers = []): ?string
    {
        return last(data_get($headers, 'Content-Type'));
    }

    /**
     * @param int $seconds
     * @return void
     */
    protected function setTokenExpiration(int $seconds): void
    {
        $this->cache->put('expiry', now()->addSeconds($seconds)->timestamp, $seconds / 60);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function url(array $params = []): string
    {
        return with($this->cache->get('geolocation') . static::API_ENDPOINT, function ($resourceUri) use ($params) {
            return empty($params) ? $resourceUri : implode('?', [$resourceUri, http_build_query($params)]);
        });
    }

    /**
     * @return bool
     */
    protected function check(): bool
    {
        return $this->exists('access_token') && !$this->expired();
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function exists(string $key): bool
    {
        return !blank($this->cache->get($key));
    }

    /**
     * @return bool
     */
    protected function expired(): bool
    {
        if ($this->exists('expiry')) {
            return now()->gte(Carbon::createFromTimestamp($this->cache->get('expiry')));
        }

        return true;
    }
}
