<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsManager
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $managerRole = Role::where('value', '=', 'manager')->first();

        if (Auth::user() && $managerRole && Auth::user()->role_id == $managerRole->id) {
            return $next($request);
        }

        return response(['message' => 'Unauthorized'], 401);
    }
}
