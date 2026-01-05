<?php

namespace Zamion101\WideEvents\Facades;

use Illuminate\Support\Facades\Facade;
use Zamion101\WideEvents\Contracts\WideEventLoggerContract;

/**
 * @method static void set(string $key, mixed $value)
 * @method static void push(string $key, mixed $value)
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void forget(string $key)
 * @method static bool has(string $key)
 * @method static bool hasError()
 * @method static void captureException(\Throwable $exception)
 * @method static \Throwable getError()
 * @method static void flush()
 * @method static array toArray()
 *
 * @see \Zamion101\WideEvents\WideEventLoggerContract
 */
class WideEvents extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WideEventLoggerContract::class;
    }
}
