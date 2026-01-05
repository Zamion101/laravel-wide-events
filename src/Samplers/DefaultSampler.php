<?php

namespace Zamion101\WideEvents\Samplers;

use Illuminate\Http\Request;
use Illuminate\Support\Lottery;
use Zamion101\WideEvents\Contracts\WideEventLoggerContract;
use Zamion101\WideEvents\Contracts\WideEventSampler;

class DefaultSampler implements WideEventSampler
{
    public function __construct(
        protected Request $request,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function shouldSample(WideEventLoggerContract $event): bool
    {
        // Always sample events with errors
        if ($event->hasError()) {
            return true;
        }

        if ($this->request->path() === 'livewire/update') {
            return false;
        }

        // Always sample events with non-200 HTTP Status Codes
        if ($event->has('response.status_code')) {
            $httpStatusCode = $event->get('response.status_code');
            if ($httpStatusCode >= 400 && $httpStatusCode < 600) {
                return true;
            }
            if ($httpStatusCode >= 300 && $httpStatusCode < 400) {
                return false;
            }
        }

        $samplingProbability = (float) config('wide-events.sampling.sampling_probability');

        $event->set('debug.sampling_probability', $samplingProbability);

        return Lottery::odds(1, 100)->choose() <= $samplingProbability * 100;
    }
}
