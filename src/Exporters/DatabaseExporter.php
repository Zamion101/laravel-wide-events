<?php

namespace Zamion101\WideEvents\Exporters;

use Illuminate\Database\Eloquent\Model;
use Zamion101\WideEvents\Contracts\WideEventLoggerContract;
use Zamion101\WideEvents\Contracts\WideEventExporter;
use Zamion101\WideEvents\Exceptions\InvalidConfiguration;
use Zamion101\WideEvents\Models\WideEvent;

class DatabaseExporter implements WideEventExporter
{

    /**
     * Stores the Wide Event in database
     */
    public function flush(WideEventLoggerContract $event): void
    {
        // TODO: Implement flush() method.
    }

    /**
     * @throws InvalidConfiguration
     */
    private function determineWideEventModel(): string
    {
        $wideEventModel = config('wide-events.exporters.database.model', WideEvent::class);
        if (!is_a($wideEventModel, WideEvent::class, true)
            || !is_a($wideEventModel, Model::class, true)) {
            throw InvalidConfiguration::modelIsNotValid($wideEventModel);
        }
        return $wideEventModel;
    }

    /**
     * @throws InvalidConfiguration
     */
    private function getWideEventModelInstance(): WideEventLoggerContract
    {
        $wideEventModel = $this->determineWideEventModel();
        return new $wideEventModel();
    }
}
