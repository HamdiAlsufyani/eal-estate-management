<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CityService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return City::query()
            ->withCount(['districts', 'properties'])
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->sort($filters['sort'] ?? null)
            ->paginate(15)
            ->withQueryString();
    }

    public function create(array $data): City
    {
        return DB::transaction(fn () => City::create($data));
    }

    public function update(City $city, array $data): City
    {
        DB::transaction(fn () => $city->update($data));

        return $city;
    }

    public function delete(City $city): bool
    {
        if ($city->districts()->exists() || $city->properties()->exists()) {
            return false;
        }

        $city->delete();

        return true;
    }
}
