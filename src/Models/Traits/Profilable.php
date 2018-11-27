<?php

namespace VdPoel\Concur\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

trait Profilable
{
    public static function bootProfilable(): void
    {

    }

    /**
     * @return MorphTo
     */
    public function profilable(): MorphTo
    {
        return $this->morphTo();
    }
}
