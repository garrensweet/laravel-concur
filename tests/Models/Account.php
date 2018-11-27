<?php

namespace VdPoel\Concur\Test\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use VdPoel\Concur\Contracts\MakesTravelRequests;
use VdPoel\Concur\Models\Traits\HasTravelProfiles;

class Account extends Model implements Authenticatable, MakesTravelRequests
{
    use HasTravelProfiles;

    protected $fillable = [
        'event_id',
        'first_name',
        'last_name',
        'email',
        'password'
    ];

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return 'secret';
    }

    public function getRememberToken()
    {
        return 'token';
    }

    public function setRememberToken($value)
    {
        $this->setAttribute('remember_token', $value);
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function firstName(): string
    {
        // TODO: Implement firstName() method.
    }

    public function lastName(): string
    {
        // TODO: Implement lastName() method.
    }

    public function email(): string
    {
        // TODO: Implement email() method.
    }

    public function ruleClass(): string
    {
        // TODO: Implement ruleClass() method.
    }

    public function loginID(): string
    {
        // TODO: Implement loginID() method.
    }

    public function active(): bool
    {
        // TODO: Implement active() method.
    }
}
