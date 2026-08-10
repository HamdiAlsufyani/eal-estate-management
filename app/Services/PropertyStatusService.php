<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropertyStatusService
{
    /**
     * Allowed status transitions, keyed by the property's current status.
     *
     * @var array<string, array<int, string>>
     */
    private const TRANSITIONS = [
        'pending' => ['approved', 'rejected'],
        'approved' => ['rejected'],
        'rejected' => ['pending', 'approved'],
    ];

    public function changeStatus(Property $property, string $newStatus, ?string $reason, User $actor): PropertyStatusHistory
    {
        $oldStatus = $property->status;

        if ($oldStatus === $newStatus) {
            throw ValidationException::withMessages([
                'status' => "This property is already {$newStatus}.",
            ]);
        }

        if (! $this->canTransition($oldStatus, $newStatus)) {
            throw ValidationException::withMessages([
                'status' => "A property cannot move from \"{$oldStatus}\" to \"{$newStatus}\".",
            ]);
        }

        return DB::transaction(function () use ($property, $oldStatus, $newStatus, $reason, $actor) {
            $property->update(['status' => $newStatus]);

            return $property->statusHistories()->create([
                'user_id' => $actor->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $reason,
            ]);
        });
    }

    public function canTransition(?string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    /**
     * @return array<int, string>
     */
    public function availableTransitions(?string $from): array
    {
        return self::TRANSITIONS[$from] ?? [];
    }
}
