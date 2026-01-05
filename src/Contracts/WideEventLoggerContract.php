<?php

namespace Zamion101\WideEvents\Contracts;


use Throwable;

interface WideEventLoggerContract
{


    /**
     * Sets the value of tje context using the context key
     *
     * @param string $key Context key
     * @param mixed $value Context value
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Pushes the value to context using the context key
     *
     * @param string $key Context key
     * @param mixed $value Context value
     * @return void
     */
    public function push(string $key, mixed $value): void;

    /**
     * Return the value of the given context key (dot notion is supported)
     *
     * @param string $key Context key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Forgets the specific context value
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key): void;


    /**
     * Determines if the context key present in the context (dot notation is supported)
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Captures the exception and it's data
     *
     * @param Throwable $exception
     */
    public function captureException(Throwable $exception): void;

    /**
     * Determine if the given error has an error associated with it.
     *
     * @return bool
     */
    public function hasError(): bool;

    /**
     * Get the error if present.
     *
     * @return array|null
     */
    public function getError(): ?array;

    /**
     * Handles the flushing/storing of the event
     *
     * @return void
     */
    public function flush(): void;

    /**
     * Converts object to Array
     *
     * @return array
     */
    public function toArray(): array;
}
