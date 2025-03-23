<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        return response()->json(User::all());
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return response()->json(['message' => 'User created', 'user' => $user], 201);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'sometimes|string',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|min:6',
        ]);

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
