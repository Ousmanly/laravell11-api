<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        // $validated = $request->validate([
            // 'name' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required|string|min:6|confirmed',
        // ]);

        // $user = User::create([
        //     'name' => $validated['name'],
        //     'email' => $validated['email'],
        //     'password' => Hash::make($validated['password']),
        // ]);

        $validated = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        };

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'access_token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['errors'=>$e->getMessage()],403);
        }
    }

    public function login(Request $request){
        $validated = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(),403);
        };

        $credentials = ['email'=>$request->email, 'password'=>$request->password];
        try {
            if (!Auth::attempt($credentials)) {
                return response()->json(['error'=>'Invalid credentials']);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'message' => 'User connected successfully',
                'access_token' => $token,
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['errors'=>$e->getMessage()],403);
        }
    }

    public function logout(Request $request) {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User has been logged out successfully',
        ], 200);
    }

    public function getUsers(){
        $users = User::all()
        ->map(function($user){
            return [
                'id'=>$user->id,
                'name'=>$user->name,
                'email'=>$user->email,
                'password'=>$user->password,
                'created_at'=>$user->created_at->format('y-d-m'),
                'updated_at'=>$user->updated_at->format('y-d-m'),
            ];
        });
        return response()->json($users);
    }
}
