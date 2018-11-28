<?php

namespace VdPoel\Concur\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Event
 * @package VdPoel\Concur\Test\Models
 * @mixin Model
 */
class Event extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @return HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * @return HasMany
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }
}
