<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Exception\GuzzleException;
use VdPoel\Concur\Contracts\ConcurResource;

/**
 * Class User
 *
 * @package VdPoel\Concur\Api
 */
class User extends Base implements ConcurResource
{
    /**
     * @var string
     */
    protected const API_ENDPOINT = '/api/user/v1.0/user';

    /**
     * @throws GuzzleException
     */
    public function all()
    {
        $response = $this->request($this->url());

    }

    /**
     * @param array $params
     * @throws GuzzleException
     */
    public function get(array $params = [])
    {
        $response = $this->request($this->url(['loginID' => data_get($params, 'loginID')]));
    }

    /**
     * @return void
     */
    public function create()
    {
        throw new \BadMethodCallException('User creation is not supported.');
    }

    public function update()
    {
        $response = $this->request($this->url());
    }

    public function deactivate()
    {
        $response = $this->request($this->url());
    }
}
