<?php

namespace VdPoel\Concur\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use VdPoel\Concur\Models\TravelProfile;

trait HasTravelProfiles
{
    /**
     * @return MorphMany
     */
    public function travelProfiles(): MorphMany
    {
        return $this->morphMany(TravelProfile::class, 'profilable');
    }
}
