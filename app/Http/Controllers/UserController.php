<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function userRegister(Request $request)
    {
        try {
            $admin = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password)
            ]);
            if ($admin) {
                return response()->json([
                    "success" => true,
                    "data" => $admin
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "data" => null
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "data" => null,
                "error" => $th
            ]);
        }
    }

    public function userLogin(Request $request)
    {
        $credetial = $request->only(['email', 'password']);
        $admin = User::where('email', $request->email)->first();
        // dd($admin);
        if (!$token = auth()->guard('user')->attempt($credetial)) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Invalid credettials"
            ]);
        } else {
            return response()->json([
                "success" => true,
                "message" => "Login Done",
                "data" => $admin,
                "token" => $token
            ]);
        }
    }

    public function getUser()
    {
        $user = JWTAuth::parseToken()->authenticate();
        // $user = auth()->guard('user')->user();
        if ($user) {
            # code...
            return response()->json([
                "success" => true,
                "message" => "Get Authenticate user",
                "data" => $user
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Not get Authenticate user"
            ]);
        }
    }

    public function userLogout()
    {
        auth()->guard('user')->logout();
        return response()->json([
            "success" => true,
            "message" => "Logout Done",
        ]);
    }
}
