<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;

class UserController extends Controller
{
    public function index()
    {
        return UserModel::all();
    }

    public function store(Request $request)
    {
        $user = UserModel::create($request->all());
        return response()->json($user, 201);
    }

    public function show($user_id)
    {
        return UserModel::find($user_id);
    }

    public function update(Request $request, $user_id)
    {
        $user = UserModel::find($user_id);
        $user->update($request->all());
        return UserModel::find($user_id);
    }

    public function destroy($user_id)
    {
        $user = UserModel::find($user_id);
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data terhapus'
        ]);
    }
}
