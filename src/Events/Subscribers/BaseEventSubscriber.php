<?php

namespace VdPoel\Concur\Events\Subscribers;

use Illuminate\Events\Dispatcher;
use VdPoel\Concur\Api\Factory;

abstract class BaseEventSubscriber
{
    /**
     * @var Factory
     */
    protected $concur;

    /**
     * @var array
     */
    protected $events = [];

    /**
     * TravelProfileEventSubscriber constructor.
     *
     * @param Factory $concur
     */
    public function __construct(Factory $concur)
    {
        $this->concur = $concur;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher $dispatcher
     * @return void
     */
    public function subscribe($dispatcher): void
    {
        foreach ($this->events as $event) {
            $dispatcher->listen($event, sprintf('%s@%s', static::class, lcfirst(class_basename($event))));
        }
    }
}
