<?php

namespace Zamion101\WideEvents;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Spatie\Macroable\Macroable;
use Throwable;
use Zamion101\WideEvents\Contracts\WideEventExporter;
use Zamion101\WideEvents\Contracts\WideEventLoggerContract;
use Zamion101\WideEvents\Contracts\WideEventSampler;
use Zamion101\WideEvents\Samplers\DefaultSampler;

class WideEventLogger implements WideEventLoggerContract
{
    use Conditionable;
    use Macroable;

    protected ?WideEventExporter $exporter = null;

    private array $context = [];

    public function __construct(WideEventExporter $exporter)
    {
        $this->exporter = $exporter;
    }

    public function set(string $key, mixed $value): void
    {
        if (is_null($value)) {
            return;
        }

        Arr::set($this->context, $key, $value);
    }

    public function push(string $key, mixed $value): void
    {
        if (is_null($value)) {
            return;
        }

        // Get current value (default to empty array if not exists)
        $current = Arr::get($this->context, $key);

        // It's the first time we are touching this key
        if ($current === null) {
            Arr::set($this->context, $key, $value);

            return;
        }

        // Merging Associative Arrays (e.g., 'user' context)
        // If both current and new value are arrays, and the new value is associative (keyed)
        if (is_array($current) && is_array($value) && Arr::isAssoc($value)) {
            $merged = array_replace_recursive($current, $value);
            Arr::set($this->context, $key, $merged);

            return;
        }

        // Appending to a List (e.g., 'debug.notes')
        // Ensure current is an array (wrap scalars if necessary)
        $list = Arr::wrap($current);

        // If value is a list (non-assoc), merge it in. If scalar, push to end.
        if (is_array($value)) {
            $list = array_merge($list, $value);
        } else {
            $list[] = $value;
        }

        Arr::set($this->context, $key, $list);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->context, $key, $default);
    }

    public function forget(string $key): void
    {
        Arr::forget($this->context, $key);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->context, $key);
    }

    public function captureException(Throwable $exception): void
    {
        $this->push('error', [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => collect($exception->getTrace())->take(5)->toArray(), // Limit trace size
            'class' => get_class($exception),
        ]);
    }

    public function hasError(): bool
    {
        return $this->has('error');
    }

    public function getError(): ?array
    {
        return $this->get('error');
    }

    public function flush(): void
    {
        if (empty($this->context)) {
            return;
        }

        if (config('wide-events.enabled') === false) {
            $this->context = [];

            return;
        }

        $samplerClass = config('wide-events.sampling.sampler');
        $sampler = app($samplerClass);

        // If the sampler is not implemented correctly fallback to Default Sampler
        if (! $sampler instanceof WideEventSampler) {
            $sampler = app(DefaultSampler::class);
            $this->push('debug', [
                'notes' => [
                    "$samplerClass is not a instance of Zamion101\WideEvents\Contracts\WideEventSampler, used DefaultSampler as fallback.",
                ],
            ]);
        }
        $this->push('debug', [
            'sampler' => $sampler::class,
        ]);

        // If the application is not in debug more, forget the debug context
        if (config('app.debug') === false) {
            $this->forget('debug');
        }

        if (! $sampler->shouldSample($this)) {
            $this->context = [];

            return;
        }

        try {
            $this->exporter->flush($this);
        } catch (\Exception $exception) {
        }
        $this->context = [];
    }

    public function toArray(): array
    {
        return $this->context;
    }
}
