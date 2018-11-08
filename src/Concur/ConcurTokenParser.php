<?php

namespace VdPoel\Concur;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use VdPoel\Concur\Traits\Attributable;

class ConcurTokenParser implements Arrayable {

    use Attributable;

    /**
     * ConcurTokenParser constructor.
     *
     * @param string|null $token
     *
     * @throws \Throwable
     */
    public function __construct(string $token = null)
    {
        $this->parse($token);
    }

    /**
     * @param string|null $token
     *
     * @return $this
     * @throws \Throwable
     */
    public function parse(string $token = null): self
    {

        $attributes = json_decode($token, true);

        $this->fill($attributes);

        return $this;
    }

    /**
     * @return bool
     */
    public function canRefresh(): bool
    {
        return Carbon::createFromTimestamp($this->getAttribute('refresh_expires_in'))->lt(now());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->getAttributes();
    }
}