<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;

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
            'username' => 'required|string',
            'bio' =>'nullable|string',
            'birthday' => 'nullable|date',
            'phone' => 'nullable|string',
            'nik' => 'nullable|string|max:16',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'website' => 'nullable|url',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'bio' => $request->bio,
            'birthday' => $request->birthday,
            'phone' => $request->phone,
            'nik' => $request->nik,
            'gender' => $request->gender,
            'website' => $request->website,            
        ]);
        $user->role()->associate(Role::find($request->role_id));
        $user->save();

        return response()->json(['message' => 'User created', 'user' => $user], 201);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'sometimes|string',
            'username' => 'sometimes|string',
            'bio' =>'sometimes|string',
            'birthday' => 'sometimes|date',
            'phone' => 'sometimes|string',
            'nik' => 'sometimes|string|max:16',
            'website' => 'sometimes|url',
            'role_id' => 'sometimes|exists:roles,id',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|min:6',
        ]);

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = Hash::make($request->password);
        if ($request->has('role_id')) $user->role_id = $request->role_id;
        if ($request->has('username')) $user->username = $request->username;
        if ($request->has('bio')) $user->bio = $request->bio;
        if ($request->has('birthday')) $user->birthday = $request->birthday;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('nik')) $user->nik = $request->nik;
        if ($request->has('website')) $user->website = $request->website;
        
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
