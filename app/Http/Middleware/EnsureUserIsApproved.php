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
                ->with('error', __('messages.account_pending_review'));
        }

        if ($user->status === 'rejected') {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', __('messages.account_rejected'));
        }

        return $next($request);
    }
}