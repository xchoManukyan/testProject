<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class IsUser
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Application|ResponseFactory|Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $userRole = Role::where('value', '=', 'user')->first();

        if (Auth::user() && $userRole && Auth::user()->role_id == $userRole->id) {
            return $next($request);
        }

        return response(['message' => 'Unauthorized'], 401);
    }
}
