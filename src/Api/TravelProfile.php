<?php

namespace VdPoel\Concur\Api;

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
     * @return string
     */
    protected function getApiPath(): string
    {
        return '/api/travelprofile/v2.0/profile';
    }

    /**
     * @param array $params
     * @throws GuzzleException
     */
    public function get(array $params = []): void
    {
        try {
            $response = $this->request($this->url(array_merge($params, [
                'userid_type' => data_get($params, 'userid_type', 'login')
            ])));

            $parsed = $this->parseResponse($response);

            event(TravelProfileFound::class, $parsed);
        } catch (GuzzleException $exception) {
            $this->errorHandler->handle($exception);
        }
    }

    /**
     * @param array $params
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function create(array $params = []): ResponseInterface
    {
        if (str_contains($loginId = data_get($params, 'LoginID'), '+')) {
            data_set($params, 'LoginID', implode('@', [str_before($loginId, '+'), str_after($loginId, '@')]));
        }

        if (!array_key_exists('Password', $params)) {
            data_set($params, 'Password', bcrypt(str_random(32)));
        }

        if (!array_key_exists('TravelConfigID', $params)) {
            data_set($params, 'TravelConfigID', config('concur.company.travel_config_id'));
        }

        $body = sprintf('<ProfileResponse xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Action="Create" LoginId="%s">', data_get($params, 'LoginID'));
        $body .= '<General>';
        $this->addField($body, 'CostCenter', $params);
        $this->addField($body, 'FirstName', $params);
        $this->addField($body, 'LastName', $params);
        $this->addField($body, 'TravelConfigID', $params);
        $body .= '</General>';
        $body .= '<EmailAddresses>';
        $body .= sprintf('<EmailAddress Contact="true" Type="Business">%s</EmailAddress>', data_get($params, 'LoginID'));
        $body .= '</EmailAddresses>';
        $this->addField($body, 'Password', $params);
        $body .= '</ProfileResponse>';

        return $this->request($this->url(), 'POST', compact('body'));
    }
}
