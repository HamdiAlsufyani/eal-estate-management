<?php

namespace App\Services;

use App\Models\PropertyType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PropertyTypeService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return PropertyType::query()
            ->withCount('properties')
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->sort($filters['sort'] ?? null)
            ->paginate(15)
            ->withQueryString();
    }

    public function create(array $data): PropertyType
    {
        $data['name'] = $data['name_en'];

        return DB::transaction(fn () => PropertyType::create($data));
    }

    public function update(PropertyType $propertyType, array $data): PropertyType
    {
        $data['name'] = $data['name_en'];

        DB::transaction(fn () => $propertyType->update($data));

        return $propertyType;
    }

    public function delete(PropertyType $propertyType): bool
    {
        if ($propertyType->properties()->exists()) {
            return false;
        }

        $propertyType->delete();

        return true;
    }
}
