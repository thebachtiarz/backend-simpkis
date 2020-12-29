<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AppKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header(env('X_API_KEY_NAME'))) {
            // ?: check into database, if key is exist, then request can next
            $checkApiKey = $request->header(env('X_API_KEY_NAME')) == env('X_APP_KEY_TOKEN');
            if ($checkApiKey) return $next($request);
            else return response()->json('Your API KEY is invalid', 403);
        }
        abort(403, 'You need an API KEY');
    }
}
