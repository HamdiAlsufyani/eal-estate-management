<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class DistrictController extends Controller
{
    /**
     * Lightweight JSON endpoint powering the public properties search's
     * dependent city → district filter.
     */
    public function byCity(City $city): JsonResponse
    {
        return response()->json(
            $city->districts()->orderBy('name_en')->get(['id', 'name_en', 'name_ar'])
                ->map(fn ($district) => ['id' => $district->id, 'name' => $district->name])
        );
    }
}
