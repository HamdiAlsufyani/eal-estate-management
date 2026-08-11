<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;

class InquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('inquiries.view');
    }

    public function view(User $user, Inquiry $inquiry): bool
    {
        return $user->can('inquiries.view')
            || $user->id === $inquiry->property?->user_id
            || $user->id === $inquiry->user_id;
    }

    public function update(User $user, Inquiry $inquiry): bool
    {
        return $user->can('inquiries.manage')
            || $user->id === $inquiry->property?->user_id;
    }
}
