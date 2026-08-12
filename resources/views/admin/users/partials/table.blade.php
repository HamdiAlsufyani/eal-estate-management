@php
    $selectableIds = $users->getCollection()->reject(fn ($user) => $user->is(auth()->user()))->pluck('id')->values();
    $statusVariants = ['active' => 'success', 'rejected' => 'danger', 'pending' => 'warning'];
@endphp

@if ($users->isEmpty())
    <x-ui.empty-state title="{{ __('users.no_users_found') }}" description="{{ __('users.no_users_found_hint') }}">
        <x-slot name="action">
            @can('create', \App\Models\User::class)
                <x-ui.button :href="route('admin.users.create')" variant="primary">{{ __('users.create_user') }}</x-ui.button>
            @endcan
        </x-slot>
    </x-ui.empty-state>
@else
    <x-ui.table>
        <x-slot name="head">
            <th class="w-10">
                <input
                    type="checkbox"
                    class="h-4 w-4 rounded border-border text-primary focus:ring-primary-hover"
                    @change="checked = $event.target.checked ? {{ Illuminate\Support\Js::from($selectableIds) }} : []"
                    :checked="checked.length > 0 && checked.length === {{ $selectableIds->count() }}"
                />
            </th>
            <th>{{ __('users.user') }}</th>
            <th class="hidden md:table-cell">{{ __('users.phone') }}</th>
            <th>{{ __('users.role') }}</th>
            <th>{{ __('messages.status') }}</th>
            <th class="hidden lg:table-cell">{{ __('properties.created') }}</th>
            <th class="text-right">{{ __('messages.actions') }}</th>
        </x-slot>

        @foreach ($users as $user)
            <tr>
                <td>
                    @unless ($user->is(auth()->user()))
                        <input type="checkbox" name="ids[]" value="{{ $user->id }}" x-model="checked" class="h-4 w-4 rounded border-border text-primary focus:ring-primary-hover" />
                    @endunless
                </td>

                <td>
                    <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3">
                        <x-ui.avatar :user="$user" size="sm" />
                        <span class="min-w-0">
                            <span class="flex items-center gap-2">
                                <span class="block truncate font-medium text-text">{{ $user->name }}</span>
                                @if ($user->is(auth()->user()))
                                    <x-ui.badge variant="primary">{{ __('users.you') }}</x-ui.badge>
                                @endif
                            </span>
                            <span class="block truncate text-xs text-text-muted">{{ $user->email }}</span>
                        </span>
                    </a>
                </td>

                <td class="hidden md:table-cell text-text-muted">{{ $user->phone }}</td>

                <td>
                    <x-ui.badge variant="gray">{{ $user->getRoleNames()->first() ?? '—' }}</x-ui.badge>
                </td>

                <td>
                    <x-ui.badge :variant="$statusVariants[$user->status] ?? 'gray'">{{ __('users.status.' . $user->status) }}</x-ui.badge>
                </td>

                <td class="hidden lg:table-cell text-text-muted">{{ $user->created_at->format('M j, Y') }}</td>

                <td class="text-right">
                    <x-ui.dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button type="button" class="btn-icon" aria-label="{{ __('messages.actions_for', ['name' => $user->name]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-ui.dropdown-item :href="route('admin.users.show', $user)">{{ __('properties.view_details') }}</x-ui.dropdown-item>

                            @can('update', $user)
                                <x-ui.dropdown-item :href="route('admin.users.edit', $user)">{{ __('messages.edit') }}</x-ui.dropdown-item>
                            @endcan

                            @can('approve', $user)
                                @if ($user->status === 'pending')
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                        @csrf @method('PATCH')
                                        <x-ui.dropdown-item class="text-success hover:!text-success">{{ __('users.approve') }}</x-ui.dropdown-item>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.reject', $user) }}">
                                        @csrf @method('PATCH')
                                        <x-ui.dropdown-item class="text-danger hover:!text-danger">{{ __('users.reject') }}</x-ui.dropdown-item>
                                    </form>
                                @elseif ($user->status === 'active')
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                        @csrf @method('PATCH')
                                        <x-ui.dropdown-item class="text-warning hover:!text-warning">{{ __('users.suspend') }}</x-ui.dropdown-item>
                                    </form>
                                @elseif ($user->status === 'rejected')
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                        @csrf @method('PATCH')
                                        <x-ui.dropdown-item class="text-success hover:!text-success">{{ __('users.activate') }}</x-ui.dropdown-item>
                                    </form>
                                @endif
                            @endcan

                            @can('delete', $user)
                                <div class="dropdown-divider"></div>
                                <x-ui.dropdown-item
                                    type="button"
                                    class="text-danger hover:!text-danger"
                                    @click="confirmTarget = { mode: 'single', url: '{{ route('admin.users.destroy', $user) }}', label: @js($user->name) }; $dispatch('open-modal', 'delete-confirm')"
                                >
                                    {{ __('messages.delete') }}
                                </x-ui.dropdown-item>
                            @endcan
                        </x-slot>
                    </x-ui.dropdown>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
@endif
