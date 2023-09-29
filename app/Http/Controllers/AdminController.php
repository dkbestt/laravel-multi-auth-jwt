<?php

namespace App\Http\Controllers;

use App\Mail\SendMailable;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{

    public function adminRegister(Request $request)
    {
        try {
            $admin = Admin::create([
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

    public function adminLogin(Request $request)
    {
        $credetial = $request->only(['email', 'password']);
        $admin = Admin::where('email', $request->email)->first();
        // dd($admin);
        if (!$token = auth()->guard('admin')->attempt($credetial)) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Invalid credentials"
            ]);
        } else {
            try {
                Mail::to("divyangkanpariya083@gmail.com")->send(new SendMailable($admin->name));
                return response()->json([
                    "success" => true,
                    "message" => "Login Done",
                    "data" => $admin,
                    "token" => $token
                ]);
            } catch (\Throwable $th) {
                if ($th->getCode() == 554) {
                    return response()->json([
                        "success" => false,
                        "message" => "Invalid Email Address"
                    ], 500);
                }
                return response()->json([
                    "success" => false,
                    "message" => $th->getMessage()
                ], 500);
            }
        }
    }

    public function getAdmin()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $admin = auth()->guard('admin')->user();
        return response()->json([
            "success" => true,
            "message" => "Get Authenticate admin.",
            "data" => $admin
        ]);
    }

    public function adminLogout()
    {
        auth()->guard('admin')->logout();
        return response()->json([
            "success" => true,
            "message" => "Logout Done",
        ]);
    }
}
