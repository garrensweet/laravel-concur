<?php

namespace VdPoel\Concur\Jobs;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LookupTravelProfile
 * @package VdPoel\Concur\Jobs
 */
class LookupTravelProfile extends BaseTravelProfile
{
    /**
     * Execute the job.
     *
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    public function handle()
    {
        try {
            return $this->concur->travelProfile->get(['userid_value' => request()->user()->getAttribute('email')]);
        } catch (ClientException $exception) {
            return null;
        }
    }
}
