<?php

namespace VdPoel\Concur\Events\TravelProfile;

class TravelProfileCreated
{
    /**
     * @var mixed
     */
    public $payload;

    /**
     * Create constructor.
     * @param mixed $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
