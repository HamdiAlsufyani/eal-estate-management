<?php

namespace App\Services;

use App\Models\District;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DistrictService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return District::query()
            ->with('city')
            ->withCount('properties')
            ->search($filters['search'] ?? null)
            ->city($filters['city'] ?? null)
            ->sort($filters['sort'] ?? null)
            ->paginate(15)
            ->withQueryString();
    }

    public function create(array $data): District
    {
        $data['name'] = $data['name_en'];

        return DB::transaction(fn () => District::create($data));
    }

    public function update(District $district, array $data): District
    {
        $data['name'] = $data['name_en'];

        DB::transaction(fn () => $district->update($data));

        return $district;
    }

    public function delete(District $district): bool
    {
        if ($district->properties()->exists()) {
            return false;
        }

        $district->delete();

        return true;
    }
}
