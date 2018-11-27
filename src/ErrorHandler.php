<?php

namespace VdPoel\Concur;

use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Psr7\parse_query;
use VdPoel\Concur\Events\TravelProfile\LoginIdInUse;
use VdPoel\Concur\Events\TravelProfile\TravelProfileNotFound;
use VdPoel\Concur\Events\UnknownErrorOccurred;

class ErrorHandler
{
    /**
     * @param GuzzleException $exception
     *
     * @return void
     */
    public function handle(GuzzleException $exception): void
    {
//        dd($exception->getRequest());
        $xml = simplexml_load_string($exception->getResponse()->getBody()->getContents());

        if (property_exists($xml, 'Message')) {
            $code = $this->extractErrorCode(data_get($xml, 'Message'));

            switch ($code) {
                case 'EC1': // Invalid user
                    $query = parse_query($exception->getRequest()->getUri()->getQuery());
                    $model = app('concur.auth.model')::where('email', data_get($query, 'userid_value'))->first();
                    event(TravelProfileNotFound::class, $model);
                    break;
                case 'E002': // LoginID in use
                    event(LoginIdInUse::class, data_get($xml, 'Message'));
                    break;
                case 'W017': // Invalid LoginID
                    break;
                default:
                    event(UnknownErrorOccurred::class, data_get($xml, 'Message'));
            }
        }
    }

    /**
     * @param string $message
     * @return string
     */
    protected function extractErrorCode(string $message): string
    {
        preg_match('|\(([A-Z0-9]+)\):|', $message, $matches);

        return $matches[1];
    }
}
