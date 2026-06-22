<?php

/**
 * Stub types for IDE/static analysis only.
 * Tidak dipakai saat runtime.
 */

namespace Illuminate\Routing\Router;

/** @deprecated IDE stub */
class PendingRouteHandle
{
    /** @return static */
    public function name(string $name): self { return $this; }
    /** @return static */
    public function middleware($middleware): self { return $this; }
    /** @return static */
    public function prefix(string $prefix): self { return $this; }
    /** @return static */
    public function group(
        callable $callback
    ): self { $callback($this); return $this; }
}

namespace Illuminate\Routing\Route;

/** @deprecated IDE stub */
class Route
{
    /** @return static */
    public function name(string $name): self { return $this; }
    /** @return static */
    public function withoutMiddleware($middleware): self { return $this; }
}

namespace Illuminate\Support;

/** @deprecated IDE stub */
class Str {}

