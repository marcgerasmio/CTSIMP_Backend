<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'valid_id' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validIdPath = $request->file('valid_id')->store('valid_ids', 'public');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'valid_id' => $validIdPath,
            'location' => $request->location,
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

    
        $user = User::where('email', $request->email)->first();

     
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

     
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed', 
        ]);
        $user = User::where('name', $request->name)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json(['message' => 'Password changed successfully']);
    }

    public function updateUser(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'status' => 'required|string|max:255',
        'remarks' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }
    $user->status = $request->status;
    $user->remarks = $request->remarks;
    $user->save();

    return response()->json(['message' => 'Status and remarks updated successfully', 'user' => $user]);
}

public function getPendingUsers()
{
    $pendingUsers = User::where('status', 'pending')->get();

    return response()->json([
        'message' => 'Pending users fetched successfully',
        'users' => $pendingUsers
    ]);
}
}
