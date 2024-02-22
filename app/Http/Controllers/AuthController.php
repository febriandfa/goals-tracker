<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                "email" => "required|email|unique:users,email",
                "password" => "required|string|min:8",
                "name" => "nullable|string|max:255",
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $user = User::create([
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'name' => $request->input('name')
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true, 'data' => $user, 'message' => 'Register Berhasil', 'token' => $token 
            ]);
        }
        catch (Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        try{
            if (! Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Login berhasil',
                'token' => $token,
                'type_token' => 'Bearer',
            ]);
        }catch (Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        try{
            Auth::user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil',
            ]);
        }catch (Exception $e){
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
