<?php

namespace VdPoel\Concur\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'LoginID',
        'EmployeeID',
        'FirstName',
        'LastName',
        'MiddleName',
        'PrimaryEmail',
        'Active',
        'CellPhoneNumber',
        'OrganizationUnit'
    ];
}
