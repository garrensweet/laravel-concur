<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use VdPoel\Concur\Events\TravelProfile\TravelProfileFound;

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
            $response = $this->request($this->url(array_merge($params, [
                'userid_type' => data_get($params, 'userid_type', 'login')
            ])));

            $parsed = $this->parseResponse($response);

            event(TravelProfileFound::class, $parsed);
        } catch (ClientException $exception) {
            $this->errorHandler->handle($exception);
        }

        return null;
    }

    /**
     * @param array $params
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function create(array $params = [])
    {
        if (!array_key_exists('CostCenter', $params)) {
            $params['CostCenter'] = null;
        }

        if (!array_key_exists('Password', $params)) {
            $params['Password'] = bcrypt(openssl_random_pseudo_bytes(32));
        }

        if (!array_key_exists('TravelConfigID', $params)) {
            $params['TravelConfigID'] = config('concur.company.travel_config_id');
        }

$xml = <<<XML
<ProfileResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Action="Create" LoginId="{$params['LoginID']}">
    <General>
        <CostCenter>{$params['CostCenter']}</CostCenter>
        <FirstName>{$params['FirstName']}</FirstName>
        <LastName>{$params['LastName']}</LastName>
        <TravelConfigID>{$params['TravelConfigID']}</TravelConfigID>
    </General>
    <EmailAddresses>
        <EmailAddress Contact="true" Type="Business">{$params['LoginID']}</EmailAddress>
    </EmailAddresses>
    <Password>{$params['Password']}</Password>
</ProfileResponse>
XML;

        return $this->request($this->url(), 'POST', ['body' => $xml]);
    }
}
