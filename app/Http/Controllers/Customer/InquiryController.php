<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $inquiries = $request->user()->inquiries()
            ->with(['property', 'property.user'])
            ->latest()
            ->paginate(15);

        return view('customer.inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry): View
    {
        $this->authorize('view', $inquiry);

        return view('customer.inquiries.show', [
            'inquiry' => $inquiry->load(['property.media', 'property.city', 'property.district', 'property.user']),
        ]);
    }
}
