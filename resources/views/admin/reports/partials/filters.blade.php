<form
    method="GET"
    action="{{ route('admin.reports.index') }}"
    class="mb-6 grid grid-cols-1 gap-3 border-b border-border pb-6 sm:grid-cols-2 lg:grid-cols-12 lg:items-end"
>
    <input type="hidden" name="type" value="{{ $type }}">

    <div class="lg:col-span-2">
        <x-ui.input type="date" name="date_from" label="{{ __('analytics.from') }}" :value="$filters['date_from'] ?? ''" />
    </div>

    <div class="lg:col-span-2">
        <x-ui.input type="date" name="date_to" label="{{ __('analytics.to') }}" :value="$filters['date_to'] ?? ''" />
    </div>

    @if (in_array($type, ['properties', 'views', 'favorites']))
        <div class="lg:col-span-2">
            <x-ui.select name="owner" label="{{ __('properties.owner') }}" placeholder="{{ __('properties.all_owners') }}" :options="$owners" :selected="$filters['owner'] ?? null" />
        </div>

        <div class="lg:col-span-2">
            <x-ui.select name="city" label="{{ __('properties.city') }}" placeholder="{{ __('properties.all_cities') }}" :options="$cities" :selected="$filters['city'] ?? null" />
        </div>

        <div class="lg:col-span-2">
            <x-ui.select name="property_type" label="{{ __('properties.property_type') }}" placeholder="{{ __('properties.all_types') }}" :options="$propertyTypes" :selected="$filters['property_type'] ?? null" />
        </div>
    @endif

    @if ($type === 'properties')
        <div class="lg:col-span-2">
            <x-ui.select
                name="purpose"
                label="{{ __('properties.purpose_label') }}"
                placeholder="{{ __('messages.all') }}"
                :options="['sale' => __('properties.purpose.sale'), 'rent' => __('properties.purpose.rent')]"
                :selected="$filters['purpose'] ?? null"
            />
        </div>

        <div class="lg:col-span-2">
            <x-ui.select
                name="status"
                label="{{ __('messages.status') }}"
                placeholder="{{ __('messages.all') }}"
                :options="['pending' => __('properties.status.pending'), 'approved' => __('properties.status.approved'), 'rejected' => __('properties.status.rejected')]"
                :selected="$filters['status'] ?? null"
            />
        </div>

        <div class="lg:col-span-2">
            <x-ui.select
                name="availability"
                label="{{ __('properties.availability_label') }}"
                placeholder="{{ __('messages.all') }}"
                :options="[
                    'available' => __('properties.availability.available'),
                    'reserved' => __('properties.availability.reserved'),
                    'sold' => __('properties.availability.sold'),
                    'rented' => __('properties.availability.rented'),
                ]"
                :selected="$filters['availability'] ?? null"
            />
        </div>
    @endif

    @if ($type === 'users')
        <div class="lg:col-span-2">
            <x-ui.select
                name="role"
                label="{{ __('users.role') }}"
                placeholder="{{ __('users.all_roles') }}"
                :options="['Admin' => 'Admin', 'Staff' => 'Staff', 'Owner' => 'Owner']"
                :selected="$filters['role'] ?? null"
            />
        </div>

        <div class="lg:col-span-2">
            <x-ui.select
                name="status"
                label="{{ __('messages.status') }}"
                placeholder="{{ __('messages.all') }}"
                :options="['pending' => __('users.status.pending'), 'active' => __('users.status.active'), 'rejected' => __('users.status.rejected')]"
                :selected="$filters['status'] ?? null"
            />
        </div>
    @endif

    @if ($type === 'inquiries')
        <div class="lg:col-span-2">
            <x-ui.select
                name="status"
                label="{{ __('messages.status') }}"
                placeholder="{{ __('inquiries.all_statuses') }}"
                :options="['new' => __('inquiries.status.new'), 'read' => __('inquiries.status.read'), 'closed' => __('inquiries.status.closed')]"
                :selected="$filters['status'] ?? null"
            />
        </div>

        <div class="lg:col-span-2">
            <x-ui.select name="property" label="{{ __('inquiries.property') }}" placeholder="{{ __('properties.all_properties') }}" :options="$properties" :selected="$filters['property'] ?? null" />
        </div>

        <div class="lg:col-span-2">
            <x-ui.select name="owner" label="{{ __('properties.owner') }}" placeholder="{{ __('properties.all_owners') }}" :options="$owners" :selected="$filters['owner'] ?? null" />
        </div>
    @endif

    <div class="flex items-center gap-2 lg:col-span-2">
        <x-ui.button type="submit" variant="primary">{{ __('analytics.apply') }}</x-ui.button>

        @if (array_filter(array_diff_key($filters, ['type' => null])))
            <x-ui.button :href="route('admin.reports.index', ['type' => $type])" variant="ghost">{{ __('analytics.reset') }}</x-ui.button>
        @endif
    </div>
</form>
