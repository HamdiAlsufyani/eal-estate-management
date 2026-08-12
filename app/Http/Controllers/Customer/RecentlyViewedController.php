<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\PropertyViewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecentlyViewedController extends Controller
{
    public function __construct(private readonly PropertyViewService $propertyViews)
    {
    }

    public function index(Request $request): View
    {
        $purpose = in_array($request->query('purpose'), ['sale', 'rent'], true)
            ? $request->query('purpose')
            : null;

        $properties = $this->propertyViews->paginateForUser($request->user(), $purpose, 20);

        return view('customer.recently-viewed.index', [
            'properties' => $properties,
            'purpose' => $purpose,
        ]);
    }

    public function destroy(Request $request, Property $property): RedirectResponse
    {
        $this->propertyViews->remove($request->user(), $property);

        return back()->with('success', __('messages.record_deleted'));
    }

    public function clear(Request $request): RedirectResponse
    {
        $this->propertyViews->clear($request->user());

        return redirect()->route('customer.recently-viewed')->with('success', __('customer.recently_viewed_cleared'));
    }
}
