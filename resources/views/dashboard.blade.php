<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-text">{{ __('dashboard.title') }}</h1>
            <p class="text-sm text-text-muted">{{ __('dashboard.overview_subtitle') }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.stat-card label="{{ __('dashboard.total_properties') }}" :value="number_format($stats['total_properties'])" variant="primary">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.pending_properties') }}" :value="number_format($stats['pending_properties'])" variant="warning">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.approved_properties') }}" :value="number_format($stats['approved_properties'])" variant="success">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.rejected_properties') }}" :value="number_format($stats['rejected_properties'])" variant="danger">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75 14.25 14.25M14.25 9.75 9.75 14.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.total_users') }}" :value="number_format($stats['total_users'])" variant="primary">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.pending_users') }}" :value="number_format($stats['pending_users'])" variant="warning">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>

            <x-ui.stat-card label="{{ __('dashboard.property_views') }}" :value="number_format($stats['property_views'])" variant="info">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </x-slot>
            </x-ui.stat-card>
        </div>

        {{-- Latest Properties --}}
        <x-ui.card title="{{ __('dashboard.recent_properties') }}">
            @if ($latestProperties->isEmpty())
                <x-ui.empty-state :title="__('dashboard.no_properties_yet')" :description="__('dashboard.new_properties_hint')" />
            @else
                <x-ui.table>
                    <x-slot name="head">
                        <th>{{ __('properties.property_title') }}</th>
                        <th>{{ __('properties.city') }}</th>
                        <th>{{ __('properties.type') }}</th>
                        <th>{{ __('properties.price') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('dashboard.submitted') }}</th>
                    </x-slot>

                    @foreach ($latestProperties as $property)
                        <tr>
                            <td class="font-medium text-text">{{ $property->title }}</td>
                            <td>{{ $property->city->name ?? '—' }}</td>
                            <td>{{ $property->propertyType->name ?? '—' }}</td>
                            <td>{{ number_format($property->price, 0) }}</td>
                            <td>
                                <x-ui.badge :variant="match($property->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">
                                    {{ __('properties.status.' . $property->status) }}
                                </x-ui.badge>
                            </td>
                            <td class="text-text-muted">{{ $property->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>
            @endif
        </x-ui.card>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Recent Users --}}
            <x-ui.card title="{{ __('dashboard.recent_users') }}">
                @if ($recentUsers->isEmpty())
                    <x-ui.empty-state :title="__('dashboard.no_users_yet')" />
                @else
                    <x-ui.table>
                        <x-slot name="head">
                            <th>{{ __('users.name') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('users.joined') }}</th>
                        </x-slot>

                        @foreach ($recentUsers as $user)
                            <tr>
                                <td>
                                    <p class="font-medium text-text">{{ $user->name }}</p>
                                    <p class="text-xs text-text-muted">{{ $user->email }}</p>
                                </td>
                                <td>
                                    <x-ui.badge :variant="match($user->status) { 'active' => 'success', 'rejected' => 'danger', default => 'warning' }">
                                        {{ __('users.status.' . $user->status) }}
                                    </x-ui.badge>
                                </td>
                                <td class="text-text-muted">{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            </x-ui.card>

            {{-- Latest Inquiries --}}
            <x-ui.card title="{{ __('dashboard.latest_inquiries') }}">
                @if ($latestInquiries->isEmpty())
                    <x-ui.empty-state :title="__('inquiries.no_inquiries')" />
                @else
                    <x-ui.table>
                        <x-slot name="head">
                            <th>{{ __('inquiries.property') }}</th>
                            <th>{{ __('inquiries.from') }}</th>
                            <th>{{ __('messages.status') }}</th>
                        </x-slot>

                        @foreach ($latestInquiries as $inquiry)
                            <tr>
                                <td class="font-medium text-text">{{ $inquiry->property->title ?? '—' }}</td>
                                <td>{{ $inquiry->user->name ?? '—' }}</td>
                                <td>
                                    <x-ui.badge :variant="match($inquiry->status) { 'new' => 'info', 'closed' => 'gray', default => 'primary' }">
                                        {{ __('inquiries.status.' . $inquiry->status) }}
                                    </x-ui.badge>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-admin-layout>
