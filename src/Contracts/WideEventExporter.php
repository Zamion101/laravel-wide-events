<?php

namespace Zamion101\WideEvents\Contracts;

interface WideEventExporter
{

    /**
     * Flushes/Stores the event
     *
     * @param WideEventLoggerContract $event
     * @return void
     */
    public function flush(WideEventLoggerContract $event): void;

}
