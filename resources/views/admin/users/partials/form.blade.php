@php
    $isCreate = ! isset($user);
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    <div class="md:col-span-2 space-y-5">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.input
                name="name"
                label="Full Name"
                placeholder="John Doe"
                value="{{ old('name', $user->name ?? '') }}"
                required
            />

            <x-ui.input
                name="email"
                type="email"
                label="Email Address"
                placeholder="john@example.com"
                value="{{ old('email', $user->email ?? '') }}"
                required
            />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <x-ui.input
                name="phone"
                type="tel"
                label="Phone Number"
                placeholder="05xxxxxxxx"
                value="{{ old('phone', $user->phone ?? '') }}"
                required
            />

            <x-ui.select
                name="role"
                label="Role"
                placeholder="Select a role"
                :options="$roles->mapWithKeys(fn ($role) => [$role => $role])->all()"
                :selected="old('role', $user?->getRoleNames()->first())"
                required
            />
        </div>

        @if ($isCreate)
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <x-ui.input name="password" type="password" label="Password" required />
                <x-ui.input name="password_confirmation" type="password" label="Confirm Password" required />
            </div>
        @endif

        <x-ui.select
            name="status"
            label="Status"
            :options="['pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected']"
            :selected="old('status', $user->status ?? 'pending')"
            required
        />
    </div>

    <div class="space-y-2" x-data="{ preview: @js($user?->profilePhotoUrl()) }">
        <label class="field-label">Profile Photo</label>

        <div class="flex flex-col items-center gap-4 rounded-[var(--radius-control)] border border-dashed border-border bg-background/60 p-6 text-center">
            <template x-if="preview">
                <img :src="preview" alt="Preview" class="h-24 w-24 rounded-full object-cover ring-2 ring-surface shadow-soft" />
            </template>
            <template x-if="!preview">
                <span class="flex h-24 w-24 items-center justify-center rounded-full bg-primary text-2xl font-semibold text-white ring-2 ring-surface shadow-soft">
                    @if ($user)
                        {{ $user->initials() }}
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    @endif
                </span>
            </template>

            <label class="btn btn-outline cursor-pointer">
                <span>{{ $isCreate ? 'Upload Photo' : 'Change Photo' }}</span>
                <input
                    type="file"
                    name="profile_photo"
                    accept="image/*"
                    class="hidden"
                    @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : @js($user?->profilePhotoUrl())"
                />
            </label>

            <p class="text-xs text-text-muted">PNG or JPG, up to 2MB.</p>

            @error('profile_photo')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
