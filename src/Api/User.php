<?php

namespace VdPoel\Concur\Api;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

/**
 * Class User
 *
 * @package VdPoel\Concur\Api
 */
class User extends Resource
{
    /**
     * @param array $params
     * @return mixed|string
     */
    public function get(array $params = [])
    {
        try {
            $this->apiVersion = 'v3.0';

            $headers = [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => sprintf('Bearer %s', $this->getCachedData('access_token'))
            ];

            $query = [];

            data_set($query, 'user', data_get($params, 'email'));
            data_set($query, 'lastName', data_get($params, 'last_name'));
            data_set($query, 'primaryEmail', data_get($params, 'email'));
            data_set($query, 'limit', data_get($params, 'limit', 25));
            data_set($query, 'active', filter_var(data_get($params, 'active', true), FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false');

            $response = $this->request($this->url($query), 'GET', compact('headers'));

            return $this->parseResponse($response);
        } catch (GuzzleException $exception) {
            $this->errorHandler->handle($exception);
        }

        return null;
    }

    public function bulkUpdateActivation(array $users, string $active = 'Y')
    {
        try {
            $this->apiVersion = 'v1.0';

            $contents = [];

            $fields = array_collapse(json_decode(json_encode($this->fields()), true));

            $required = array_filter($fields, function ($field) {
                return (data_get($field, 'Required') === 'Y') && !in_array($field['Id'], ['Active', 'LoginId']);
            });

            while (!empty($users)) {
                $limit = count($users) >= static::BATCH_MAX_ITEMS ? static::BATCH_MAX_ITEMS : count($users);

                $chunk = array_splice($users, 0, $limit);

                $body = '<batch xmlns="http://www.concursolutions.com/api/user/2011/02">';

                for ($index = 0; $index < count($chunk); ++$index) {
                    $empId = data_get($chunk[$index], 'EmployeeID');
                    $loginId = data_get($chunk[$index], 'LoginID');
                    $body .= '<UserProfile>';
                    $this->addField($body, 'FeedRecordNumber', ['FeedRecordNumber' => $index + 1]);
                    foreach ($required as $item) {
                        $tag = $item['Id'];
                        $value = data_get($chunk[$index], $item['Id']);

                        $body .= sprintf('<%s>%s</%s>', $tag, $value, $tag);
                    }
                    $this->addField($body, 'Active', ['Active' => $active]);
                    $body .= "<LoginID>$loginId</LoginID>";
//                    $body .= sprintf('<Password>%s</Password>', null);
                    if (blank($empId)) {
                        $uuid = Str::uuid()->toString();
                        $body .= "<EmployeeID>$loginId</EmployeeID>";
                        $body .= "<NewEmployeeID>$uuid</NewEmployeeID>";
                    } else {
                        $body .= "<EmployeeID>$empId</EmployeeID>";
                    }
                    $body .= '</UserProfile>';
                }

                $body .= '</batch>';

//                dd($body);

                $response = $this->request($this->url(), 'POST', compact('body'));

                dd($response->getBody()->getContents());

                $contents[] = $this->parseResponse($response);
            }

            return $contents;
        } catch (GuzzleException $exception) {
            $this->errorHandler->handle($exception);
        }
    }

    protected function fields ()
    {
        $this->apiVersion = 'v1.0';

        $response = $this->request('https://us.api.concursolutions.com/api/user/v1.0/FormFields');

        return $this->parseResponse($response);
    }

    /**
     * @return string
     */
    protected function getRequestUrl(): string
    {
        switch ($this->apiVersion) {
            case 'v1.0':
                $host = 'https://us.api.concursolutions.com';
                break;
            case 'v3.0':
                $host = 'https://www.concursolutions.com';
                break;
            default:
                $host = '';
        }
        return implode(DIRECTORY_SEPARATOR, [$host, trim($this->getApiPath(), DIRECTORY_SEPARATOR)]);
    }

    /**
     * @return string
     */
    protected function getApiPath(): string
    {
        switch ($this->apiVersion) {
            case 'v1.0':
                return '/api/user/v1.0/users';
                break;
            case 'v3.0':
                return '/api/v3.0/common/users';
                break;
            default:
                return '';
        }
    }
}
