<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Exception\GuzzleException;
use VdPoel\Concur\Contracts\ConcurResource;

class TravelProfile extends Base implements ConcurResource
{
    /**
     * @var string
     */
    protected const API_ENDPOINT = '/api/travelprofile/v2.0/profile';

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
        $response = $this->request($this->url([
            'userid_type' => data_get($params, 'userid_type', 'login'),
            'userid_value' => data_get($params, 'userid_value'),
        ]));
    }

    public function create()
    {
        $response = $this->request($this->url(), 'POST');
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
