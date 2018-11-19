<?php

namespace VdPoel\Concur\Jobs;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Psr\Http\Message\ResponseInterface;
use VdPoel\Concur\Api\Factory;

/**
 * Class BaseTravelProfile
 * @package VdPoel\Concur\Jobs
 */
abstract class BaseTravelProfile
{
    /**
     * @var Authenticatable
     */
    protected $user;

    /**
     * @var Factory
     */
    protected $concur;

    /**
     * Create a new job instance.
     *
     * @param  Authenticatable $user
     * @param Factory $concur
     */
    public function __construct(Authenticatable $user, Factory $concur)
    {
        $this->user   = $user;
        $this->concur = $concur;
    }

    /**
     * Execute the job.
     *
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    abstract public function handle();
}
