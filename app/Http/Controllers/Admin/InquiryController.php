<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inquiry\UpdateInquiryStatusRequest;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\User;
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
        $this->authorize('viewAny', Inquiry::class);

        $filters = $request->only(['search', 'status', 'property', 'owner', 'date_from', 'date_to', 'sort']);

        return view('admin.inquiries.index', [
            'inquiries' => $this->inquiries->paginateForAdmin($filters),
            'filters' => $filters,
            'properties' => Property::query()->orderBy('title_en')->get()->pluck('title', 'id'),
            'owners' => User::query()->role('Owner')->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function show(Inquiry $inquiry): View
    {
        $this->authorize('view', $inquiry);

        return view('admin.inquiries.show', [
            'inquiry' => $inquiry->load(['property.user', 'user']),
        ]);
    }

    public function updateStatus(UpdateInquiryStatusRequest $request, Inquiry $inquiry): RedirectResponse
    {
        $this->inquiries->updateStatus($inquiry, $request->validated('status'));

        return back()->with('success', __('messages.inquiry_status_updated'));
    }
}
