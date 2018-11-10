<?php

namespace VdPoel\Concur\Api;

use Carbon\Carbon;
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
     * @throws GuzzleException
     */
    public function get(array $params = [])
    {
        $response = $this->request($this->url(['LoginId' => data_get($params, 'LoginId')]));
    }

    public function update(array $params = [])
    {
        $response = $this->request($this->url());
    }

    public function fields()
    {

    }

    /**
     * @param Carbon|string $deactivatedAt
     * @throws GuzzleException
     */
    public function deactivate($deactivatedAt)
    {
        $deactivatedAt = is_string($deactivatedAt) ? Carbon::parse($deactivatedAt) : $deactivatedAt;

        $response = $this->request($this->url());
    }

    /**
     * @param string $LoginID
     * @throws GuzzleException
     */
    public function changePassword(string $LoginID)
    {
        $response = $this->request($this->url(), 'POST');
    }
}
