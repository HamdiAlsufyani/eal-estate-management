<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Property\ChangePropertyAvailabilityRequest;
use App\Http\Requests\Admin\Property\ChangePropertyStatusRequest;
use App\Http\Requests\Admin\Property\StorePropertyRequest;
use App\Http\Requests\Admin\Property\UpdatePropertyRequest;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Services\PropertyService;
use App\Services\PropertyStatusService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PropertyController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly PropertyService $properties,
        private readonly PropertyStatusService $propertyStatus,
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Property::class);

        $actor = $request->user();
        $filters = $request->only([
            'search', 'purpose', 'status', 'availability',
            'property_type', 'city', 'district', 'owner', 'featured', 'sort',
        ]);

        return view('admin.properties.index', [
            'properties' => $this->properties->paginate($filters, $actor),
            'filters' => $filters,
            'propertyTypes' => PropertyType::query()->orderBy('name_en')->get()->pluck('name', 'id'),
            'cities' => City::query()->orderBy('name_en')->get()->pluck('name', 'id'),
            'districts' => $this->properties->selectableDistricts(! empty($filters['city']) ? (int) $filters['city'] : null),
            'owners' => $actor->canManageAllProperties()
                ? User::query()->role('Owner')->orderBy('name')->pluck('name', 'id')
                : collect(),
            'canManageAll' => $actor->canManageAllProperties(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Property::class);

        return view('admin.properties.create', $this->properties->formData(auth()->user()));
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $property = $this->properties->create($request->validated(), $request->user());

        return redirect()
            ->route('admin.properties.show', $property)
            ->with('success', __('messages.property_created'));
    }

    public function show(Property $property): View
    {
        $this->authorize('view', $property);

        $property->load(['user', 'propertyType', 'city', 'district', 'amenities', 'media']);
        $property->loadCount('views');

        $statusHistories = $property->statusHistories()
            ->with('user')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.properties.show', [
            'property' => $property,
            'statusHistories' => $statusHistories,
            'availableTransitions' => $this->propertyStatus->availableTransitions($property->status),
        ]);
    }

    public function edit(Property $property): View
    {
        $this->authorize('update', $property);

        return view('admin.properties.edit', array_merge(
            $this->properties->formData(auth()->user(), $property),
            ['property' => $property->load(['amenities', 'media'])]
        ));
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $this->properties->update($property, $request->validated(), $request->user());

        return redirect()
            ->route('admin.properties.show', $property)
            ->with('success', __('messages.property_updated'));
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $this->properties->delete($property);

        return redirect()
            ->route('admin.properties.index')
            ->with('success', __('messages.property_deleted'));
    }

    public function changeStatus(ChangePropertyStatusRequest $request, Property $property): RedirectResponse
    {
        $status = $request->validated('status');

        $this->propertyStatus->changeStatus(
            $property,
            $status,
            $request->validated('reason'),
            $request->user(),
        );

        $message = match ($status) {
            'approved' => __('messages.property_approved'),
            'rejected' => __('messages.property_rejected'),
            default => __('messages.property_status_updated'),
        };

        return back()->with('success', $message);
    }

    public function changeAvailability(ChangePropertyAvailabilityRequest $request, Property $property): RedirectResponse
    {
        $this->properties->changeAvailability($property, $request->validated('availability'));

        return back()->with('success', __('messages.property_availability_updated'));
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
