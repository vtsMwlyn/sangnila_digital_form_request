<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLeaveBalance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->leave_balance == 0 && Auth::user()->overwork_balance == 0) {
            return response()->view('leave-limit');
        }

        return $next($request);
    }
}
