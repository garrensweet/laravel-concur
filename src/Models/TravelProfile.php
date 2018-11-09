<?php

namespace VdPoel\Concur\Models;

use Illuminate\Database\Eloquent\Model;

class TravelProfile extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'NamePrefix',
        'FirstName',
        'MiddleName',
        'LastName',
        'NameSuffix',
        'PreferredName',
        'JobTitle',
        'CompanyEmployeeID',
        'PreferredLanguage',
        'HasOpenBooking',
        'CountryCode',
        'CompanyName',
        'CompanyID',
        'RuleClass',
        'TravelConfigID',
        'AgencyNumber',
        'UUID'
    ];
}
