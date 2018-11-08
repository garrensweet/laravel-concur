<?php

namespace VdPoel\Concur;

use Illuminate\Contracts\Support\Arrayable;
use VdPoel\Concur\Traits\Attributable;

class ConcurCredentials implements Arrayable {

    use Attributable;

    protected $fillable = [
        'client_id',
        'client_secret',
        'grant_type',
        'username',
        'password'
    ];

    /**
     * Credentials constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->getAttributes();
    }

}