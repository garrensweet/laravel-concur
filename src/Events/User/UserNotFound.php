<?php

namespace VdPoel\Concur\Events\User;

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
