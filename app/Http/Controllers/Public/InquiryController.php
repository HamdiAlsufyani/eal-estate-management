<?php

namespace App\Http\Controllers\Public;

use App\Events\InquiryCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreInquiryRequest;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request, Property $property): RedirectResponse
    {
        abort_unless($property->status === 'approved', 404);

        $hasActiveInquiry = $property->inquiries()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['new', 'read'])
            ->exists();

        if ($hasActiveInquiry) {
            return back()->with('error', __('inquiries.already_submitted'));
        }

        $inquiry = $property->inquiries()->create([
            'user_id' => $request->user()->id,
            'phone' => $request->validated('phone'),
            'message' => $request->validated('message'),
            'status' => 'new',
        ]);

        InquiryCreated::dispatch($inquiry);

        return back()->with('success', __('messages.inquiry_submitted'));
    }
}
