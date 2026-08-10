<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Amenity\StoreAmenityRequest;
use App\Http\Requests\Admin\Amenity\UpdateAmenityRequest;
use App\Models\Amenity;
use App\Services\AmenityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmenityController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly AmenityService $amenities)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Amenity::class);

        $filters = $request->only(['search', 'status', 'sort']);

        return view('admin.amenities.index', [
            'amenities' => $this->amenities->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Amenity::class);

        return view('admin.amenities.create');
    }

    public function store(StoreAmenityRequest $request): RedirectResponse
    {
        $amenity = $this->amenities->create($request->validated());

        return redirect()
            ->route('admin.amenities.show', $amenity)
            ->with('success', "Amenity \"{$amenity->name}\" was created successfully.");
    }

    public function show(Amenity $amenity): View
    {
        $this->authorize('view', $amenity);

        $amenity->loadCount('properties');

        return view('admin.amenities.show', [
            'amenity' => $amenity,
        ]);
    }

    public function edit(Amenity $amenity): View
    {
        $this->authorize('update', $amenity);

        return view('admin.amenities.edit', [
            'amenity' => $amenity,
        ]);
    }

    public function update(UpdateAmenityRequest $request, Amenity $amenity): RedirectResponse
    {
        $this->amenities->update($amenity, $request->validated());

        return redirect()
            ->route('admin.amenities.show', $amenity)
            ->with('success', "Amenity \"{$amenity->name}\" was updated successfully.");
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        $this->authorize('delete', $amenity);

        $name = $amenity->name;

        if (! $this->amenities->delete($amenity)) {
            return back()->with('error', 'This amenity cannot be deleted because it is assigned to properties.');
        }

        return redirect()
            ->route('admin.amenities.index')
            ->with('success', "Amenity \"{$name}\" was deleted successfully.");
    }
}
