@php
    $statusVariants = ['active' => 'success', 'rejected' => 'danger', 'pending' => 'warning'];
@endphp

<x-admin-layout title="{{ $user->name }}" :breadcrumbs="[['label' => 'Users', 'url' => route('admin.users.index')], ['label' => $user->name]]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text">{{ $user->name }}</h1>
                <p class="text-sm text-text-muted">User account details and activity.</p>
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                @can('update', $user)
                    <x-ui.button :href="route('admin.users.edit', $user)" variant="outline" size="sm">Edit</x-ui.button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div x-data="{ confirmDelete: false }" class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Profile --}}
            <x-ui.card class="lg:col-span-1">
                <div class="flex flex-col items-center text-center">
                    <x-ui.avatar :user="$user" size="xl" />

                    <h2 class="mt-4 text-lg font-semibold text-text">{{ $user->name }}</h2>
                    <p class="text-sm text-text-muted">{{ $user->email }}</p>

                    <div class="mt-3 flex items-center gap-2">
                        <x-ui.badge variant="gray">{{ $user->getRoleNames()->first() ?? '—' }}</x-ui.badge>
                        <x-ui.badge :variant="$statusVariants[$user->status] ?? 'gray'">{{ ucfirst($user->status) }}</x-ui.badge>
                    </div>
                </div>

                <dl class="mt-6 space-y-4 border-t border-border pt-6 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Phone</dt>
                        <dd class="font-medium text-text">{{ $user->phone }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Created</dt>
                        <dd class="font-medium text-text">{{ $user->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-text-muted">Last Login</dt>
                        <dd class="font-medium text-text">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Stats + Actions --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <x-ui.stat-card label="Properties" :value="number_format($user->properties_count)" variant="primary">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12 11.204 3.045c.44-.44 1.152-.44 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </x-slot>
                    </x-ui.stat-card>

                    <x-ui.stat-card label="Favorites" :value="number_format($user->favorites_count)" variant="secondary">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                        </x-slot>
                    </x-ui.stat-card>

                    <x-ui.stat-card label="Inquiries" :value="number_format($user->inquiries_count)" variant="info">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                            </svg>
                        </x-slot>
                    </x-ui.stat-card>
                </div>

                <x-ui.card title="Account Actions">
                    <div class="flex flex-wrap gap-3">
                        @can('approve', $user)
                            @if ($user->status === 'pending')
                                <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                    @csrf @method('PATCH')
                                    <x-ui.button type="submit" variant="success" size="sm">Approve</x-ui.button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.reject', $user) }}">
                                    @csrf @method('PATCH')
                                    <x-ui.button type="submit" variant="danger" size="sm">Reject</x-ui.button>
                                </form>
                            @elseif ($user->status === 'active')
                                <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                    @csrf @method('PATCH')
                                    <x-ui.button type="submit" variant="outline" size="sm">Suspend</x-ui.button>
                                </form>
                            @elseif ($user->status === 'rejected')
                                <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                    @csrf @method('PATCH')
                                    <x-ui.button type="submit" variant="success" size="sm">Activate</x-ui.button>
                                </form>
                            @endif
                        @endcan

                        @can('update', $user)
                            <x-ui.button :href="route('admin.users.edit', $user)" variant="outline" size="sm">Edit User</x-ui.button>
                        @endcan

                        @can('delete', $user)
                            <x-ui.button type="button" variant="danger" size="sm" class="ml-auto" @click="confirmDelete = true; $dispatch('open-modal', 'delete-user')">
                                Delete User
                            </x-ui.button>
                        @endcan
                    </div>
                </x-ui.card>
            </div>
        </div>

        @can('delete', $user)
            <x-ui.modal name="delete-user" max-width="md">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-text">Delete User</h3>
                    <p class="mt-2 text-sm text-text-muted">
                        Are you sure you want to delete <span class="font-medium text-text">{{ $user->name }}</span>? This action cannot be undone.
                    </p>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-user')">Cancel</x-ui.button>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="danger">Delete</x-ui.button>
                        </form>
                    </div>
                </div>
            </x-ui.modal>
        @endcan
    </div>
</x-admin-layout>
