<?php

namespace Zamion101\WideEvents\Contracts;

interface WideEventSampler
{
    /**
     * Determine if the given event should be persisted to storage.
     *
     * @param  WideEventLoggerContract  $event  The full collected wide event
     */
    public function shouldSample(WideEventLoggerContract $event): bool;
}
