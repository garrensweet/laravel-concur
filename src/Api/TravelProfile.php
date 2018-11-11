<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

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
     * @param array $params
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    public function get(array $params = [])
    {
        try {
            return $this->request($this->url(array_merge($params, [
                'userid_type' => data_get($params, 'userid_type', 'login')
            ])));
        } catch (ClientException $exception) {
            return null;
        }
    }

    /**
     * @param array $params
     * @return mixed
     * @throws GuzzleException
     */
    public function create(array $params = [])
    {
        $params['TravelConfigID'] = config('concur.company.travel_config_id');
        $params['Password']       = bcrypt(openssl_random_pseudo_bytes(32));

$xml = <<<XML
<ProfileResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Action="Create" LoginId="{$params['LoginID']}">
    <General>
        <FirstName>{$params['FirstName']}</FirstName>
        <LastName>{$params['LastName']}</LastName>
        <TravelConfigID>{$params['TravelConfigID']}</TravelConfigID>
    </General>
    <Password>{$params['Password']}</Password>
</ProfileResponse>
XML;

        return $this->request($this->url(), 'POST', ['body' => $xml]);
    }
}
