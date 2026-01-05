<?php

namespace Zamion101\WideEvents\Contracts;

use Throwable;

interface WideEventLoggerContract
{
    /**
     * Sets the value of tje context using the context key
     *
     * @param  string  $key  Context key
     * @param  mixed  $value  Context value
     */
    public function set(string $key, mixed $value): void;

    /**
     * Pushes the value to context using the context key
     *
     * @param  string  $key  Context key
     * @param  mixed  $value  Context value
     */
    public function push(string $key, mixed $value): void;

    /**
     * Return the value of the given context key (dot notion is supported)
     *
     * @param  string  $key  Context key
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Forgets the specific context value
     */
    public function forget(string $key): void;

    /**
     * Determines if the context key present in the context (dot notation is supported)
     */
    public function has(string $key): bool;

    /**
     * Captures the exception and it's data
     */
    public function captureException(Throwable $exception): void;

    /**
     * Determine if the given error has an error associated with it.
     */
    public function hasError(): bool;

    /**
     * Get the error if present.
     */
    public function getError(): ?array;

    /**
     * Handles the flushing/storing of the event
     */
    public function flush(): void;

    /**
     * Converts object to Array
     */
    public function toArray(): array;
}
