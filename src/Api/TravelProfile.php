<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Class TravelProfile
 * @package VdPoel\Concur\Api
 */
class TravelProfile extends Resource
{
    /**
     * @var string
     */
    protected const API_ENDPOINT = '/api/travelprofile/v2.0/profile';

    /**
     * @var string
     */
    protected const XSD = 'https://www.concursolutions.com/ns/TravelUserProfile.xsd';

    /**
     * @param array $params
     * @throws GuzzleException
     */
    public function get(array $params = [])
    {
        $response = $this->request($this->url([
            'userid_type'  => data_get($params, 'userid_type', 'login'),
            'userid_value' => data_get($params, 'userid_value'),
        ]));
    }

    /**
     * @param array $params
     * @return mixed|void
     * @throws GuzzleException
     */
    public function create(array $params = [])
    {
        $response = $this->request($this->url(), 'POST', $params);
    }

    /**
     * @param array $params
     * @return mixed|void
     * @throws GuzzleException
     */
    public function update(array $params = [])
    {
        $response = $this->request($this->url(), 'PUT', $params);
    }
}
