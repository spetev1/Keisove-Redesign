<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoGate
{
    /**
     * Hold every request behind a shared password while the site is an
     * unreleased demo, and keep it out of search results regardless.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $password = config('demo.password');

        if (blank($password)) {
            return $this->withoutIndexing($next($request));
        }

        if ($request->session()->get('demo_unlocked') === true) {
            return $this->withoutIndexing($next($request));
        }

        // The unlock endpoint has to stay reachable or there is no way in.
        if ($request->routeIs('demo.unlock')) {
            return $this->withoutIndexing($next($request));
        }

        return $this->withoutIndexing(
            response()->view('demo-gate', [
                'failed' => $request->session()->pull('demo_gate_failed', false),
            ], Response::HTTP_UNAUTHORIZED),
        );
    }

    /**
     * A tunnel URL is public, so the gate alone is not enough - a crawler that
     * reaches a cached or unlocked page should still refuse to index it.
     */
    protected function withoutIndexing(Response $response): Response
    {
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
