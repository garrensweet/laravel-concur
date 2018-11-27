<?php

namespace VdPoel\Concur\Api;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Store;
use Psr\Http\Message\ResponseInterface;
use VdPoel\Concur\ErrorHandler;

/**
 * Class Base
 * @package VdPoel\Concur\Api
 */
abstract class Base
{
    /**
     * @var int
     */
    protected const BATCH_MAX_ITEMS = 500;

    /**
     * @var int
     */
    protected const CACHE_LIFETIME = 60;

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
     * @var string
     */
    protected $apiVersion;

    /**
     * Concur constructor.
     * @param Client $client
     * @param CacheManager $cache
     */
    public function __construct(Client $client, CacheManager $cache)
    {
        $this->client       = $client;
        $this->cache        = $cache->getStore();
        $this->config       = config('concur');
        $this->errorHandler = new ErrorHandler();

        if (method_exists($this->cache, 'setPrefix')) {
            $this->cache->setPrefix(implode('.', ['Concur', $this->getCachePrefix()]));
        }
    }

    /**
     * @return string
     */
    protected function getCachePrefix(): string
    {
        return class_basename(static::class);
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function parseResponse(ResponseInterface $response)
    {
        $contentType = $this->getContentType($response->getHeaders());

        switch (true) {
            case str_contains($contentType, 'application/xml'):
                return simplexml_load_string($response->getBody()->getContents());
                break;
            case str_contains($contentType, 'application/json'):
                $contents = json_decode($response->getBody(), true);
                return is_array($contents) && json_last_error() === JSON_ERROR_NONE ? $contents : null;
                break;
            default:
                return null;
        }
    }

    /**
     * @param array $headers
     * @return string|null
     */
    protected function getContentType(array $headers = []): ?string
    {
        return last(data_get($headers, 'Content-Type'));
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
     * @param int $seconds
     * @return void
     */
    protected function setTokenExpiration(int $seconds): void
    {
        $this->cache->put('expiry', now()->addSeconds($seconds)->timestamp, $seconds / 60);
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
     * @param string $key
     * @return bool
     */
    protected function exists(string $key): bool
    {
        return !blank($this->cache->get($key));
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function request(string $url, string $method = 'GET', array $options = []): ResponseInterface
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
     * @param array $params
     * @return string
     */
    protected function url(array $params = []): string
    {
        return empty($params) ? $this->getRequestUrl() : implode('?', [$this->getRequestUrl(), http_build_query($params)]);
    }

    /**
     * @return string
     */
    protected function getRequestUrl(): string
    {
        $baseUrl = $this->cache->get('geolocation') ?? config('concur.api.urls.geolocation');

        return implode(DIRECTORY_SEPARATOR, [
            trim($baseUrl, DIRECTORY_SEPARATOR),
            trim($this->getApiPath(), DIRECTORY_SEPARATOR)
        ]);
    }

    /**
     * @return bool
     */
    protected function check(): bool
    {
        return $this->exists('access_token') && !$this->expired();
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

    /**
     * @param string $body
     * @param string $tag
     * @param array $params
     * @param null $default
     * @return void
     */
    protected function addField(string &$body, string $tag, array $params, $default = null): void
    {
        if (!blank($value = data_get($params, $tag, $default))) {
            $body .= sprintf('<%s>%s</%s>', $tag, $value, $tag);
        }
    }

    /**
     * @return string
     */
    protected abstract function getApiPath (): string ;
}
