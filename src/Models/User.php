<?php

namespace VdPoel\Concur\Models\User;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Required. The record number in the current batch.
     *
     * @var int
     */
    public $feedRecordNumber;

    protected $fillable = [
        'Active',
        'EmpId',
        'LoginId',
        'LocaleName',
       'NewEmpId',
       'NewLoginId'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @return string
     */
    public function getActiveAttribute(): string
    {
        return (is_bool($this->attributes['Active']) && !!$this->attributes['Active']) ? 'Y' : 'N';
    }

    /**
     * @param string $active
     */
    public function setActiveAttribute(string $active = 'Y'): void
    {
        $this->setAttribute('Active', $active === 'Y');
    }
}
