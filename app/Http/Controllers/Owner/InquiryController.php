<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\Inquiry\UpdateInquiryStatusRequest;
use App\Models\Inquiry;
use App\Services\InquiryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly InquiryService $inquiries)
    {
    }

    public function index(Request $request): View
    {
        $inquiries = Inquiry::query()
            ->whereHas('property', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['property', 'user'])
            ->latest()
            ->paginate(15);

        return view('owner.inquiries.index', [
            'inquiries' => $inquiries,
        ]);
    }

    public function show(Inquiry $inquiry): View
    {
        $this->authorize('view', $inquiry);

        return view('owner.inquiries.show', [
            'inquiry' => $inquiry->load(['property', 'user']),
        ]);
    }

    public function updateStatus(UpdateInquiryStatusRequest $request, Inquiry $inquiry): RedirectResponse
    {
        $this->inquiries->updateStatus($inquiry, $request->validated('status'));

        return back()->with('success', __('messages.inquiry_status_updated'));
    }
}
