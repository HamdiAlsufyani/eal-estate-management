<?php

namespace App\Services;

use App\Models\Amenity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AmenityService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Amenity::query()
            ->withCount('properties')
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->sort($filters['sort'] ?? null)
            ->paginate(15)
            ->withQueryString();
    }

    public function create(array $data): Amenity
    {
        $data['name'] = $data['name_en'];

        return DB::transaction(fn () => Amenity::create($data));
    }

    public function update(Amenity $amenity, array $data): Amenity
    {
        $data['name'] = $data['name_en'];

        DB::transaction(fn () => $amenity->update($data));

        return $amenity;
    }

    public function delete(Amenity $amenity): bool
    {
        if ($amenity->properties()->exists()) {
            return false;
        }

        $amenity->delete();

        return true;
    }
}
