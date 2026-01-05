<?php

namespace Zamion101\WideEvents\Exporters;

use Illuminate\Support\Facades\Log;
use Zamion101\WideEvents\Contracts\WideEventExporter;
use Zamion101\WideEvents\Contracts\WideEventLoggerContract;

class ConsoleExporter implements WideEventExporter
{
    /**
     * {@inheritDoc}
     */
    public function flush(WideEventLoggerContract $event): void
    {
        try {
            Log::info(json_encode($event->toArray(), JSON_THROW_ON_ERROR));
        } catch (\JsonException $e) {

        }
    }
}
