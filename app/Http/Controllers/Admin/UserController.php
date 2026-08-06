<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\BulkUserActionRequest;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly UserService $users)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $filters = $request->only(['search', 'role', 'status', 'sort']);

        return view('admin.users.index', [
            'users' => $this->users->paginate($filters),
            'roles' => Role::pluck('name'),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', [
            'roles' => Role::pluck('name'),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->users->create($request->validated());

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', "User \"{$user->name}\" was created successfully.");
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load('roles')->loadCount(['properties', 'favorites', 'inquiries']);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::pluck('name'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->update($user, $request->validated());

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', "User \"{$user->name}\" was updated successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $name = $user->name;
        $this->users->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User \"{$name}\" was deleted successfully.");
    }

    public function approve(User $user): RedirectResponse
    {
        $this->authorize('approve', $user);

        $this->users->approve($user);

        return back()->with('success', "User \"{$user->name}\" was approved.");
    }

    public function reject(User $user): RedirectResponse
    {
        $this->authorize('reject', $user);

        $this->users->reject($user);

        return back()->with('success', "User \"{$user->name}\" was rejected.");
    }

    public function suspend(User $user): RedirectResponse
    {
        $this->authorize('suspend', $user);

        $this->users->suspend($user);

        return back()->with('success', "User \"{$user->name}\" was suspended.");
    }

    public function activate(User $user): RedirectResponse
    {
        $this->authorize('activate', $user);

        $this->users->activate($user);

        return back()->with('success', "User \"{$user->name}\" was activated.");
    }

    public function bulk(BulkUserActionRequest $request): RedirectResponse
    {
        $ids = $request->validated('ids');
        $actor = $request->user();

        $count = match ($request->validated('action')) {
            'delete' => $this->users->bulkDelete($ids, $actor),
            'approve', 'activate' => $this->users->bulkUpdateStatus($ids, 'active', $actor),
            'reject' => $this->users->bulkUpdateStatus($ids, 'rejected', $actor),
        };

        return back()->with('success', "{$count} user(s) updated successfully.");
    }
}
