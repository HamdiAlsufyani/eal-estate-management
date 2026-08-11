<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->user()->favorites()
            ->with(['property' => function ($query) {
                $query->withTrashed()->with([
                    'propertyType', 'city', 'district', 'media',
                    'favorites' => fn ($query) => $query->where('user_id', auth()->id()),
                ]);
            }])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('public.favorites.index', [
            'favorites' => $favorites,
        ]);
    }

    public function toggle(Request $request, Property $property): JsonResponse
    {
        $favorite = $request->user()->favorites()->where('property_id', $property->id)->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json([
                'favorited' => false,
                'count' => $property->favorites()->count(),
            ]);
        }

        abort_unless($property->status === 'approved', 404);

        $request->user()->favorites()->firstOrCreate(['property_id' => $property->id]);

        return response()->json([
            'favorited' => true,
            'count' => $property->favorites()->count(),
        ]);
    }
}
