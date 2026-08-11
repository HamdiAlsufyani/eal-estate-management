<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Favorite::class);

        $search = $request->string('search')->trim()->value() ?: null;

        $favorites = Favorite::query()
            ->with(['user', 'property'])
            ->when($search, fn (Builder $query) => $query->where(
                fn (Builder $query) => $query
                    ->whereHas('user', fn (Builder $query) => $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('property', fn (Builder $query) => $query->where('title', 'like', "%{$search}%"))
            ))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.favorites.index', [
            'favorites' => $favorites,
            'filters' => ['search' => $search],
        ]);
    }
}
