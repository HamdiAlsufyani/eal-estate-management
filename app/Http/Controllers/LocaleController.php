<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, config('app.available_locales', ['en']), true), 404);

        session(['locale' => $locale]);

        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        return redirect($request->query('redirect') ?: url()->previous());
    }
}
