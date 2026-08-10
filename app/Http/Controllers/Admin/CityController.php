<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\City\StoreCityRequest;
use App\Http\Requests\Admin\City\UpdateCityRequest;
use App\Models\City;
use App\Services\CityService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly CityService $cities)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', City::class);

        $filters = $request->only(['search', 'status', 'sort']);

        return view('admin.cities.index', [
            'cities' => $this->cities->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', City::class);

        return view('admin.cities.create');
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        $city = $this->cities->create($request->validated());

        return redirect()
            ->route('admin.cities.show', $city)
            ->with('success', "City \"{$city->name}\" was created successfully.");
    }

    public function show(City $city): View
    {
        $this->authorize('view', $city);

        $city->loadCount(['districts', 'properties']);

        return view('admin.cities.show', [
            'city' => $city,
        ]);
    }

    public function edit(City $city): View
    {
        $this->authorize('update', $city);

        return view('admin.cities.edit', [
            'city' => $city,
        ]);
    }

    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        $this->cities->update($city, $request->validated());

        return redirect()
            ->route('admin.cities.show', $city)
            ->with('success', "City \"{$city->name}\" was updated successfully.");
    }

    public function destroy(City $city): RedirectResponse
    {
        $this->authorize('delete', $city);

        $name = $city->name;

        if (! $this->cities->delete($city)) {
            return back()->with('error', 'This city cannot be deleted because it has districts or properties assigned to it.');
        }

        return redirect()
            ->route('admin.cities.index')
            ->with('success', "City \"{$name}\" was deleted successfully.");
    }
}
