<?php

namespace VdPoel\Concur\Events\TravelProfile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class UserNotFound
{
    /**
     * @var string
     */
    public $loginId;

    /**
     * Create constructor.
     * @param string $loginId
     */
    public function __construct(string $loginId)
    {
        $this->loginId = $loginId;
    }
}
