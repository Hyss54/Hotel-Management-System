<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['department', 'roles'])->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $roles = Role::all();
        return view('users.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'department_id' => 'required|exists:departments,id',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'department_id' => $request->department_id,
            'is_active' => true,
            'phone' => $request->phone,
        ]);
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['department', 'roles', 'permissions']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $departments = Department::where('is_active', true)->get();
        $roles = Role::all();
        return view('users.edit', compact('user', 'departments', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user->update($request->only(['name', 'email', 'department_id', 'phone', 'is_active']));
        if ($request->password) {
            $user->update(['password' => bcrypt($request->password)]);
        }
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id())
            return back()->with('error', 'Cannot delete your own account.');
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
