<?php

namespace App\Services\Analytics;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Backs /admin/reports. Every method returns a paginated, already-filtered
 * result set built from real aggregate queries — no in-PHP loops over full
 * tables — so a future export layer can consume these directly.
 */
class ReportsService
{
    public function properties(array $filters): LengthAwarePaginator
    {
        return Property::query()
            ->with(['user', 'propertyType', 'city', 'district'])
            ->withCount(['views', 'favorites', 'inquiries'])
            ->ownedBy($filters['owner'] ?? null)
            ->inCity($filters['city'] ?? null)
            ->ofType($filters['property_type'] ?? null)
            ->purpose($filters['purpose'] ?? null)
            ->status($filters['status'] ?? null)
            ->availabilityIs($filters['availability'] ?? null)
            ->when($filters['date_from'] ?? null, fn (Builder $q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($filters['date_to'] ?? null, fn (Builder $q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function users(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->withCount(['properties', 'favorites', 'inquiries'])
            ->role($filters['role'] ?? null)
            ->status($filters['status'] ?? null)
            ->when($filters['date_from'] ?? null, fn (Builder $q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($filters['date_to'] ?? null, fn (Builder $q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function inquiries(array $filters): LengthAwarePaginator
    {
        return Inquiry::query()
            ->with(['property.user', 'user'])
            ->status($filters['status'] ?? null)
            ->forProperty($filters['property'] ?? null)
            ->forOwner($filters['owner'] ?? null)
            ->dateFrom($filters['date_from'] ?? null)
            ->dateTo($filters['date_to'] ?? null)
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function views(array $filters): LengthAwarePaginator
    {
        return Property::query()
            ->with(['user', 'city', 'propertyType'])
            ->withCount(['views' => fn (Builder $q) => $q
                ->when($filters['date_from'] ?? null, fn (Builder $qq, $d) => $qq->whereDate('created_at', '>=', $d))
                ->when($filters['date_to'] ?? null, fn (Builder $qq, $d) => $qq->whereDate('created_at', '<=', $d))])
            ->inCity($filters['city'] ?? null)
            ->ofType($filters['property_type'] ?? null)
            ->ownedBy($filters['owner'] ?? null)
            ->having('views_count', '>', 0)
            ->orderByDesc('views_count')
            ->paginate(15)
            ->withQueryString();
    }

    public function favorites(array $filters): LengthAwarePaginator
    {
        return Property::query()
            ->with(['user', 'city', 'propertyType'])
            ->withCount(['favorites' => fn (Builder $q) => $q
                ->when($filters['date_from'] ?? null, fn (Builder $qq, $d) => $qq->whereDate('created_at', '>=', $d))
                ->when($filters['date_to'] ?? null, fn (Builder $qq, $d) => $qq->whereDate('created_at', '<=', $d))])
            ->inCity($filters['city'] ?? null)
            ->ofType($filters['property_type'] ?? null)
            ->ownedBy($filters['owner'] ?? null)
            ->having('favorites_count', '>', 0)
            ->orderByDesc('favorites_count')
            ->paginate(15)
            ->withQueryString();
    }
}
