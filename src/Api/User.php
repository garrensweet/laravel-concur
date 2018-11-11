<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class User
 *
 * @package VdPoel\Concur\Api
 */
class User extends Resource
{
    /**
     * @var string
     */
    protected const API_ENDPOINT = '/api/user/v1.0/user';

    /**
     * @param array $params
     * @return mixed|string
     */
    public function get(array $params = [])
    {
        try {
            $response = $this->request($this->url(['loginID' => data_get($params, 'email')]));

            $contents = $this->parseResponse($response);

            return $contents;
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $contents = $response->getBody()->getContents();

            $parsed = simplexml_load_string($contents);

            dd($parsed);
        } catch (GuzzleException $exception) {
            dd($exception->getMessage());
        }

        return null;
    }
}
