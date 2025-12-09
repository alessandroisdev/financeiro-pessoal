<?php

namespace App\Http\Middleware;

use Closure;

class ResourceOwner
{
    public function handle($request, Closure $next)
    {
        $resource = $request->route()->parameterNames()[0] ?? null;

        if (!$resource) {
            return $next($request);
        }

        $model = $request->route($resource);

        if ($model && method_exists($model, 'getAttribute')) {
            if ($model->user_id !== auth()->id()) {
                abort(403, 'Não autorizado.');
            }
        }

        return $next($request);
    }
}
