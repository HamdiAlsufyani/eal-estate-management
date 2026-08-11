<?php

namespace App\Services;

use App\Models\Inquiry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InquiryService
{
    public function paginateForAdmin(array $filters): LengthAwarePaginator
    {
        return Inquiry::query()
            ->with(['property.user', 'user'])
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->forProperty($filters['property'] ?? null)
            ->forOwner($filters['owner'] ?? null)
            ->dateFrom($filters['date_from'] ?? null)
            ->dateTo($filters['date_to'] ?? null)
            ->sort($filters['sort'] ?? null)
            ->paginate(15)
            ->withQueryString();
    }

    public function updateStatus(Inquiry $inquiry, string $status): Inquiry
    {
        $inquiry->update(['status' => $status]);

        return $inquiry;
    }
}
