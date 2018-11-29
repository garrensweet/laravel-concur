<?php

namespace VdPoel\Concur\Jobs;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use VdPoel\Concur\Api\Factory;

/**
 * Class BaseTravelProfile
 * @package VdPoel\Concur\Jobs
 */
abstract class BaseTravelProfile
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var Factory
     */
    protected $concur;

    /**
     * Create a new job instance.
     * @param Factory $concur
     * @param $attributes
     */
    public function __construct(Factory $concur, $attributes)
    {
        $this->concur     = $concur;
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    abstract public function handle();
}
