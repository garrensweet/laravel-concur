<?php

namespace VdPoel\Concur\Events\TravelProfile;

use Illuminate\Queue\SerializesModels;

class TravelProfileFound
{
    use SerializesModels;

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
