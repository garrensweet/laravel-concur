<?php

namespace VdPoel\Concur\Models;

use Illuminate\Database\Eloquent\Model;
use VdPoel\Concur\Models\Traits\Profilable;

class TravelProfile extends Model
{
    use Profilable;

    /**
     * @var array
     */
    protected $casts = [
        'content' => 'array'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'content',
        'profilable_type',
        'profilable_id'
    ];

    /**
     * TravelProfile constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (config('concur.migrations.tenancy.enabled')) {
            array_push($this->fillable, config('concur.migrations.tenancy.foreign_key'));
        }
    }
}
