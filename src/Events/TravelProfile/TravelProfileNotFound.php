<?php

namespace VdPoel\Concur\Events\TravelProfile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class TravelProfileNotFound
{
    use SerializesModels;

    /**
     * @var Model
     */
    public $model;

    /**
     * Create constructor.
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }
}
