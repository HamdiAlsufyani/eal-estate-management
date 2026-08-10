<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\District\StoreDistrictRequest;
use App\Http\Requests\Admin\District\UpdateDistrictRequest;
use App\Models\City;
use App\Models\District;
use App\Services\DistrictService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DistrictController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly DistrictService $districts)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', District::class);

        $filters = $request->only(['search', 'city', 'sort']);

        return view('admin.districts.index', [
            'districts' => $this->districts->paginate($filters),
            'cities' => City::query()->orderBy('name')->pluck('name', 'id'),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', District::class);

        return view('admin.districts.create', [
            'cities' => $this->selectableCities(),
        ]);
    }

    public function store(StoreDistrictRequest $request): RedirectResponse
    {
        $district = $this->districts->create($request->validated());

        return redirect()
            ->route('admin.districts.show', $district)
            ->with('success', "District \"{$district->name}\" was created successfully.");
    }

    public function show(District $district): View
    {
        $this->authorize('view', $district);

        $district->load('city')->loadCount('properties');

        return view('admin.districts.show', [
            'district' => $district,
        ]);
    }

    public function edit(District $district): View
    {
        $this->authorize('update', $district);

        return view('admin.districts.edit', [
            'district' => $district,
            'cities' => $this->selectableCities($district->city_id),
        ]);
    }

    public function update(UpdateDistrictRequest $request, District $district): RedirectResponse
    {
        $this->districts->update($district, $request->validated());

        return redirect()
            ->route('admin.districts.show', $district)
            ->with('success', "District \"{$district->name}\" was updated successfully.");
    }

    public function destroy(District $district): RedirectResponse
    {
        $this->authorize('delete', $district);

        $name = $district->name;

        if (! $this->districts->delete($district)) {
            return back()->with('error', 'This district cannot be deleted because it has properties assigned to it.');
        }

        return redirect()
            ->route('admin.districts.index')
            ->with('success', "District \"{$name}\" was deleted successfully.");
    }

    /**
     * Lightweight JSON endpoint powering dependent city → district dropdowns
     * (e.g. the Property create/edit form and its list filters).
     */
    public function byCity(Request $request, City $city): JsonResponse
    {
        // Powers the Property form's dependent dropdown, so it's gated on
        // properties.view (Owners included) rather than districts.view.
        abort_unless($request->user()->can('properties.view'), 403);

        return response()->json(
            $city->districts()->orderBy('name')->get(['id', 'name'])
        );
    }

    private function selectableCities(?int $currentCityId = null): Collection
    {
        return City::query()
            ->where(function ($query) use ($currentCityId) {
                $query->where('is_active', true);

                if ($currentCityId) {
                    $query->orWhere('id', $currentCityId);
                }
            })
            ->orderBy('name')
            ->pluck('name', 'id');
    }
}
