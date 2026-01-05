<?php

return [
    /*
     * If set to false, no events will be logged.
     */
    'enabled' => env('WIDE_EVENTS_ENABLED', true),

    /*
     * This class will be used to manage logging of the Wide Events
     * Class must implement the Zamion101\WideEvents\Contracts\WideEventLoggerContract interface.
     */
    'logger' => \Zamion101\WideEvents\WideEventLogger::class,

    /*
     * This will be used to determine sampling rules of the wide events.
     * Sampler should implement the Zamion101\WideEvents\Contracts\WideEventSampler interface.
     */
    'sampling' => [
        'sampler' => \Zamion101\WideEvents\Samplers\DefaultSampler::class,
        // Sampling probability must be between 0.0 - 1.0 (0% -> 100%), default is 1%
        'sampling_probability' => env('WIDE_EVENTS_SAMPLING_PROBABILITY', 0.1),
    ],

    /*
     * This is the list of all the available exporter options for the Wide Events
     */
    'exporters' => [
        'console' => [
            'exporter' => Zamion101\WideEvents\Exporters\ConsoleExporter::class,
        ],
        'file' => [
            'exporter' => Zamion101\WideEvents\Contracts\WideEventExporter::class,
        ],
        'database' => [
            'exporter' => Zamion101\WideEvents\Exporters\DatabaseExporter::class,
            /*
             * This model will be used to log wide events to Database.
             * It should implement the Zamion101\WideEvents\Contracts\WideEventContract interface
             * and extend Illuminate\Database\Eloquent\Model.
             */
            'model' => \Zamion101\WideEvents\Models\WideEvent::class,
            /*
             * This is the name of the table that will be created by the migration and
             * used by the Wide Event model shipped with this package.
             */
            'table_name' => env('WIDE_EVENTS_TABLE_NAME', 'wide_events'),

            /*
             * This is the database connection that will be used by the migration and
             * the Wide Event model shipped with this package. In case it's not set
             * Laravel's database.default will be used instead.
             */
            'database_connection' => env('WIDE_EVENTS_DB_CONNECTION'),
        ],
    ],

    /*
     * This will be used to configure the Request Middleware
     */
    'middleware' => [
        'enabled' => env('WIDE_EVENTS_MIDDLEWARE_ENABLED', true),
        'trace_id' => [
            // Available extractors are w3c,regex
            'extractor' => 'w3c',
            // This will only be used when extractor set to 'regex'
            'header_name' => 'X-Trace-Id',
            // Regex must have a named group 'trace_id', example for w3c trace context
            // You can use https://regexr.com/ to create regex
            'regex' => '/(?<version>[0-9]+)\-(?<trace_id>[a-f0-9]+)\-(?<span_id>[a-f0-9]+)\-(?<trace_flags>[0-9]+)/',
        ],
        'span_id' => [
            // Available extractors are w3c,regex
            'extractor' => 'w3c',
            // This will only be used when extractor set to 'regex'
            'header_name' => 'X-Span-Id',
            // Regex must have a named group 'span_id', example for w3c trace context
            // You can use https://regexr.com/ to create regex
            'regex' => '/(?<version>[0-9]+)\-(?<trace_id>[a-f0-9]+)\-(?<span_id>[a-f0-9]+)\-(?<trace_flags>[0-9]+)/',
        ],
    ],

    /*
     * This determines which exporter to use for exporting the Wide Events
     */
    'exporter' => 'database',

];
