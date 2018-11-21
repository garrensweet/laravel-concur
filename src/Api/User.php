<?php

namespace VdPoel\Concur\Api;

use function array_chunk;
use function count;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use VdPoel\Concur\Contracts\MakesTravelRequests;
use VdPoel\Concur\Models\User;

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

    public function deactivate(array $users)
    {
        try {
            if (count($users) > static::BATCH_MAX_ITEMS) {
                array_chunk($users, static::BATCH_MAX_ITEMS);
            }
            $requestor->
            $response = $this->request($this->url(), 'POST');

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
    }

    public function reactivate(User $user)
    {

    }

    protected function createXML(array $items): string
}
