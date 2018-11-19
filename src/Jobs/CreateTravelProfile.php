<?php

namespace VdPoel\Concur\Jobs;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class CreateTravelProfile extends BaseTravelProfile
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
            return $this->concur->travelProfile->create($this->mapFormParams('travel.profile'));
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param string $type
     * @return array
     */
    protected function mapFormParams(string $type): array
    {
        return $this->combineKeysAndValues(config(sprintf('concur.form_params.%s', $type)));
    }

    /**
     * @param array $map
     * @return array
     */
    protected function combineKeysAndValues(array $map): array
    {
        return with(array_values(optional(request()->user())->only(array_values($map)) ?? []), function (array $values) use ($map) {
            return array_combine(blank($values) ? [] : array_keys($map), $values);
        });
    }
}
