<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PropertyType\StorePropertyTypeRequest;
use App\Http\Requests\Admin\PropertyType\UpdatePropertyTypeRequest;
use App\Models\PropertyType;
use App\Services\PropertyTypeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyTypeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly PropertyTypeService $propertyTypes)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', PropertyType::class);

        $filters = $request->only(['search', 'status', 'sort']);

        return view('admin.property-types.index', [
            'propertyTypes' => $this->propertyTypes->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', PropertyType::class);

        return view('admin.property-types.create');
    }

    public function store(StorePropertyTypeRequest $request): RedirectResponse
    {
        $propertyType = $this->propertyTypes->create($request->validated());

        return redirect()
            ->route('admin.property-types.show', $propertyType)
            ->with('success', "Property type \"{$propertyType->name}\" was created successfully.");
    }

    public function show(PropertyType $propertyType): View
    {
        $this->authorize('view', $propertyType);

        $propertyType->loadCount([
            'properties',
            'properties as active_properties_count' => fn ($query) => $query->where('status', 'approved'),
        ]);

        return view('admin.property-types.show', [
            'propertyType' => $propertyType,
        ]);
    }

    public function edit(PropertyType $propertyType): View
    {
        $this->authorize('update', $propertyType);

        return view('admin.property-types.edit', [
            'propertyType' => $propertyType,
        ]);
    }

    public function update(UpdatePropertyTypeRequest $request, PropertyType $propertyType): RedirectResponse
    {
        $this->propertyTypes->update($propertyType, $request->validated());

        return redirect()
            ->route('admin.property-types.show', $propertyType)
            ->with('success', "Property type \"{$propertyType->name}\" was updated successfully.");
    }

    public function destroy(PropertyType $propertyType): RedirectResponse
    {
        $this->authorize('delete', $propertyType);

        $name = $propertyType->name;

        if (! $this->propertyTypes->delete($propertyType)) {
            return back()->with('error', 'This property type cannot be deleted because it has properties assigned to it.');
        }

        return redirect()
            ->route('admin.property-types.index')
            ->with('success', "Property type \"{$name}\" was deleted successfully.");
    }
}
