<?php

namespace App\Events;

use App\Models\Inquiry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InquiryStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Inquiry $inquiry,
        public readonly string $oldStatus,
        public readonly string $newStatus,
    ) {
    }
}
