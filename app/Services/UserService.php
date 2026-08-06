<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserService
{
    private const PHOTO_DIRECTORY = 'profile-photos';

    public function paginate(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->withCount(['properties', 'favorites', 'inquiries'])
            ->search($filters['search'] ?? null)
            ->role($filters['role'] ?? null)
            ->status($filters['status'] ?? null)
            ->sort($filters['sort'] ?? null)
            ->paginate(15)
            ->withQueryString();
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'status' => $data['status'],
                'password' => $data['password'],
            ]);

            if (! empty($data['profile_photo'])) {
                $user->profile_photo = $this->storePhoto($data['profile_photo']);
            }

            $user->save();
            $user->syncRoles([$data['role']]);

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->fill([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'status' => $data['status'],
            ]);

            if (! empty($data['profile_photo'])) {
                $this->deletePhoto($user->profile_photo);
                $user->profile_photo = $this->storePhoto($data['profile_photo']);
            }

            $user->save();
            $user->syncRoles([$data['role']]);

            return $user;
        });
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deletePhoto($user->profile_photo);
            $user->delete();
        });
    }

    public function approve(User $user): User
    {
        $user->update(['status' => 'active']);

        return $user;
    }

    public function reject(User $user): User
    {
        $user->update(['status' => 'rejected']);

        return $user;
    }

    public function suspend(User $user): User
    {
        $user->update(['status' => 'rejected']);

        return $user;
    }

    public function activate(User $user): User
    {
        $user->update(['status' => 'active']);

        return $user;
    }

    public function bulkUpdateStatus(array $ids, string $status, User $actor): int
    {
        return User::query()
            ->whereIn('id', $ids)
            ->whereKeyNot($actor->id)
            ->update(['status' => $status]);
    }

    public function bulkDelete(array $ids, User $actor): int
    {
        $users = User::query()
            ->whereIn('id', $ids)
            ->whereKeyNot($actor->id)
            ->get();

        return DB::transaction(function () use ($users) {
            $users->each(function (User $user) {
                $this->deletePhoto($user->profile_photo);
                $user->delete();
            });

            return $users->count();
        });
    }

    private function storePhoto(UploadedFile $photo): string
    {
        return $photo->store(self::PHOTO_DIRECTORY, 'public');
    }

    private function deletePhoto(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
