<?php

namespace VdPoel\Concur\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class UnknownErrorOccurred
{
    /**
     * @var string
     */
    public $message;

    /**
     * Create constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
