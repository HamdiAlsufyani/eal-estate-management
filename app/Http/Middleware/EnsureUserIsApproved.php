<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->status === 'pending') {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'حسابك قيد المراجعة من قبل الإدارة.');
        }

        if ($user->status === 'rejected') {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'تم رفض حسابك، يرجى التواصل مع الإدارة.');
        }

        return $next($request);
    }
}