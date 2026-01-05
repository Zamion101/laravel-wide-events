<?php

namespace Zamion101\WideEvents;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Zamion101\WideEvents\Contracts\WideEventExporter;
use Zamion101\WideEvents\Contracts\WideEventLoggerContract;

class WideEventsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-wide-events')
            ->hasConfigFile('wide-events')
            ->hasMigration('create_wide_events_table');
    }

    public function registeringPackage()
    {
        $this->app->bind(WideEventExporter::class, static function ($app) {
            $exporter = config('wide-events.exporter');
            $exporters = config('wide-events.exporters');

            if (! isset($exporters[$exporter])) {
                throw new \RuntimeException("Unknown exporter '{$exporter}'");
            }

            $exporterConfig = $exporters[$exporter];
            $className = $exporterConfig['exporter'];
            if (! is_a($className, WideEventExporter::class, true)) {
                throw new \RuntimeException("The given exporter class `{$className}` does not implement `".WideEventExporter::class.'`');
            }

            return $app->make($className);
        });

        $this->app->scoped(WideEventLoggerContract::class, static function ($app) {
            $className = config('wide-events.logger');
            if (! is_a($className, WideEventLoggerContract::class, true)) {
                throw new \RuntimeException("The given logger class `{$className}` does not implement `".WideEventLoggerContract::class.'`');
            }

            return $app->make($className);
        });

    }
}
