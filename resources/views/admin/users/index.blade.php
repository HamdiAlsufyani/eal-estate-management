<x-admin-layout title="Users" :breadcrumbs="[['label' => 'Users']]">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-text">Users</h1>
                <p class="text-sm text-text-muted">Manage accounts, roles, and access approvals.</p>
            </div>

            @can('create', \App\Models\User::class)
                <x-ui.button :href="route('admin.users.create')" variant="primary" class="hidden sm:inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New User
                </x-ui.button>
            @endcan
        </div>
    </x-slot>

    <div x-data="{ checked: [], confirmTarget: null }" class="space-y-6">
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
        @endif

        <x-ui.card>
            @include('admin.users.partials.filters')

            <form method="POST" action="{{ route('admin.users.bulk') }}" x-ref="bulkForm">
                @csrf
                <input type="hidden" name="action" x-ref="bulkAction" />

                <div x-show="checked.length > 0" x-cloak class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-[var(--radius-control)] bg-primary/5 px-4 py-3">
                    <p class="text-sm font-medium text-primary"><span x-text="checked.length"></span> user(s) selected</p>

                    <div class="flex flex-wrap gap-2">
                        @can('bulkAction', \App\Models\User::class)
                            <x-ui.button type="button" size="sm" variant="outline" @click="$refs.bulkAction.value = 'approve'; $refs.bulkForm.requestSubmit()">Approve Selected</x-ui.button>
                            <x-ui.button type="button" size="sm" variant="outline" @click="$refs.bulkAction.value = 'reject'; $refs.bulkForm.requestSubmit()">Reject Selected</x-ui.button>
                            <x-ui.button type="button" size="sm" variant="outline" @click="$refs.bulkAction.value = 'activate'; $refs.bulkForm.requestSubmit()">Activate Selected</x-ui.button>
                        @endcan

                        @can('deleteAny', \App\Models\User::class)
                            <x-ui.button
                                type="button"
                                size="sm"
                                variant="danger"
                                @click="confirmTarget = { mode: 'bulk', label: checked.length + ' selected user(s)' }; $dispatch('open-modal', 'delete-confirm')"
                            >
                                Delete Selected
                            </x-ui.button>
                        @endcan
                    </div>
                </div>

                @include('admin.users.partials.table', ['users' => $users])
            </form>
        </x-ui.card>

        @if ($users->hasPages())
            <x-ui.card class="!shadow-none">
                {{ $users->links() }}
            </x-ui.card>
        @endif

        {{-- Shared delete confirmation modal (single row + bulk) --}}
        <x-ui.modal name="delete-confirm" max-width="md">
            <div class="p-6">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-danger-soft text-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>

                <h3 class="mt-4 text-lg font-semibold text-text">Delete User</h3>
                <p class="mt-2 text-sm text-text-muted">
                    Are you sure you want to delete <span class="font-medium text-text" x-text="confirmTarget?.label"></span>?
                    This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <x-ui.button type="button" variant="outline" @click="$dispatch('close-modal', 'delete-confirm')">Cancel</x-ui.button>
                    <x-ui.button
                        type="button"
                        variant="danger"
                        @click="
                            if (confirmTarget.mode === 'bulk') {
                                $refs.bulkAction.value = 'delete';
                                $refs.bulkForm.requestSubmit();
                            } else {
                                $refs.singleDeleteForm.setAttribute('action', confirmTarget.url);
                                $refs.singleDeleteForm.requestSubmit();
                            }
                        "
                    >
                        Delete
                    </x-ui.button>
                </div>
            </div>
        </x-ui.modal>

        <form x-ref="singleDeleteForm" method="POST" action="#" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</x-admin-layout>
