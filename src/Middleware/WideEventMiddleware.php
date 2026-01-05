<?php

namespace Zamion101\WideEvents\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Zamion101\WideEvents\Contracts\WideEventLoggerContract;
use Zamion101\WideEvents\GitHash;

final class WideEventMiddleware
{

    public function __construct(
        private readonly WideEventLoggerContract $logger,
    )
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = Carbon::parse($request->server('REQUEST_TIME'));
        $request->attributes->set('__request_start_time', $startTime);
        $traceId = $this->getTraceId($request);
        $spanId = $this->getSpanId($request);

        $this->logger->set('timestamp', $startTime->toIso8601String());
        $this->logger->set('fingerprint', sha1(implode('|', array_filter(
            [$request->ip(), $request->userAgent(), $request->user()?->id],
            static fn ($el) => $el !== null
        ))));
        $this->logger->set('trace_id', $traceId);
        $this->logger->set('span_id', $spanId);
        $this->logger->set('service', config('app.name'));
        $this->logger->set('commit_hash', GitHash::get());

        $this->logger->push('request', [
            'method' => $request->method(),
            'full_url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'query' => $request->query(),
            'size_bytes' => (int)$request->server('CONTENT_LENGTH'),
            'headers' => $request->headers->all(),
        ]);

        if ($user = $request->user()) {
            $this->logger->push('user', [
                'id' => $user->id,
                'email' => $user->email,
                'email_verified' => $user->email_verified_at !== null,
                'account_age_days' => round(Carbon::parse($request->user()->created_at)->diffInDays(Carbon::today())),
            ]);
        }

        try {
            return $next($request);
        } catch (\Exception $exception) {
            $this->logger->captureException($exception);
            throw $exception;
        } finally {
            $endTime = Carbon::now();
            $durationMs = $startTime->diffInMilliseconds($endTime);
            $this->logger->set('response.duration_ms', $durationMs);
        }

    }

    /**
     * Terminate runs after the response is sent.
     * We use this to actually write to the DB.
     */
    public function terminate(Request $request, Response $response): void
    {
        $this->logger->set('debug.terminate_time', Carbon::now()->toDateTimeString());
        $this->logger->push('response', [
            'status_code' => $response->getStatusCode(),
            'content_type' => $response->headers->get('CONTENT_TYPE'),
        ]);

        $this->logger->flush();
    }

    /**
     * Extracts the trace id from W3C Trace Context Header or creates one
     */
    private function getTraceId(Request $request): ?string
    {
        $traceIdConfig = config('wide-events.middleware.trace_id');
        $extractor = $traceIdConfig['extractor'];

        // W3C Trace Context format: version-trace_id-span_id-trace_flags
        // Example: 00-0af7651916cd43dd8448eb211c80319c-b7ad6b7169203331-01
        if ($extractor === 'w3c') {
            $traceParent = $request->header('traceparent');
            if ($traceParent === null) {
                return null;
            }
            $parts = explode('-', $traceParent);
            if (count($parts) >= 2 && strlen($parts[1]) === 32) {
                return $parts[1];
            }
        } else if ($extractor === 'regex') {
            if (!isset($traceIdConfig['header_name'])) {
                return null;
            }
            $traceHeader = $request->header($traceIdConfig['header_name']);
            if ($traceHeader === null) {
                return null;
            }
            preg_match($traceIdConfig['regex'], $traceHeader, $matches);
            if (isset($matches['trace_id'])) {
                return $matches['trace_id'];
            }
        }

        return null;
    }

    /**
     * Extracts the trace id from W3C Trace Context Header or creates one
     */
    private function getSpanId(Request $request): ?string
    {
        $spanIdConfig = config('wide-events.middleware.span_id');
        $extractor = $spanIdConfig['extractor'];

        // W3C Trace Context format: version-trace_id-span_id-trace_flags
        // Example: 00-0af7651916cd43dd8448eb211c80319c-b7ad6b7169203331-01
        if ($extractor === 'w3c') {
            $traceParent = $request->header('traceparent');
            if ($traceParent === null) {
                return null;
            }
            $parts = explode('-', $traceParent);
            if (count($parts) >= 2 && strlen($parts[1]) === 32) {
                return $parts[1];
            }
        } else if ($extractor === 'regex') {
            if (!isset($spanIdConfig['header_name'])) {
                return null;
            }
            $spanHeader = $request->header($spanIdConfig['header_name']);
            if ($spanHeader === null) {
                return null;
            }
            preg_match($spanIdConfig['regex'], $spanHeader, $matches);
            if (isset($matches['trace_id'])) {
                return $matches['trace_id'];
            }
        }

        return null;
    }
}
