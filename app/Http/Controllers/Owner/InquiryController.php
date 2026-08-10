<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
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
}
