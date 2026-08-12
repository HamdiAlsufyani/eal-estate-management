<?php

namespace App\Http\Controllers\Owner;

use App\Events\PropertyCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Property\StorePropertyRequest;
use App\Http\Requests\Admin\Property\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PropertyController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly PropertyService $properties)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        $actor = $request->user();
        $filters = $request->only(['search', 'purpose', 'status', 'availability', 'property_type', 'city', 'district', 'sort']);

        return view('owner.properties.index', [
            'properties' => $this->properties->paginate($filters, $actor),
            'filters' => $filters,
            'propertyTypes' => $this->properties->selectablePropertyTypes(),
            'cities' => $this->properties->selectableCities(),
            'districts' => $this->properties->selectableDistricts(! empty($filters['city']) ? (int) $filters['city'] : null),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Property::class);

        return view('owner.properties.create', $this->properties->formData(auth()->user()));
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $property = $this->properties->create($request->validated(), $request->user());

        PropertyCreated::dispatch($property);

        return redirect()
            ->route('owner.properties.show', $property)
            ->with('success', __('messages.property_created'));
    }

    public function show(Property $property): View
    {
        $this->authorize('view', $property);

        $property->load(['propertyType', 'city', 'district', 'amenities', 'media']);
        $property->loadCount('views');

        $statusHistories = $property->statusHistories()
            ->with('user')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('owner.properties.show', [
            'property' => $property,
            'statusHistories' => $statusHistories,
        ]);
    }

    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        return view('owner.properties.edit', array_merge(
            $this->properties->formData(auth()->user(), $property),
            ['property' => $property->load(['amenities', 'media'])]
        ));
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $this->properties->update($property, $request->validated(), $request->user());

        return redirect()
            ->route('owner.properties.show', $property)
            ->with('success', __('messages.property_updated'));
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $this->properties->delete($property);

        return redirect()
            ->route('owner.properties.index')
            ->with('success', __('messages.property_deleted'));
    }

    public function destroyImage(Property $property, Media $media): RedirectResponse
    {
        $this->authorize('update', $property);

        abort_unless($media->model_type === Property::class && $media->model_id === $property->id, 404);

        $media->delete();

        return back()->with('success', __('messages.image_removed'));
    }

    public function reorderImages(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'media' => ['required', 'array'],
            'media.*' => ['integer'],
        ]);

        $this->properties->reorderImages($property, $request->input('media'));

        return back()->with('success', __('messages.image_order_updated'));
    }
}
