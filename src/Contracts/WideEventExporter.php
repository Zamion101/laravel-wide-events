<?php

namespace Zamion101\WideEvents\Contracts;

interface WideEventExporter
{
    /**
     * Flushes/Stores the event
     */
    public function flush(WideEventLoggerContract $event): void;
}
