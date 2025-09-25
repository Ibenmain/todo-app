<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use \Tymon\JWTAuth\Exceptions\TokenInvalidException;


class AuthController extends Controller {

    public function register(Request $r) {
        $validated = $r->validate([
            'full_name'=>'required|string',
            'email'=>'required|email|unique:users',
            'phone'=>'nullable',
            'address'=>'nullable',
            'image'=>'nullable|image',
            'password'=>'required|min:6|confirmed'
        ]);

        if($r->hasFile('image')) {
            $path = $r->file('image')->store('users','public');
            $validated['image'] = $path;
        }

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        $token = auth('api')->login($user);
        return response()->json(['user'=>$user,'token'=>$token],201);
    }

    public function login(Request $r) {

        $credentials = $r->only('email','password');
        
        if(!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error'=>'Invalid credentials'],401);
        }

        return response()->json(['access_token'=>$token,
        'token_type'=>'bearer',
        'expires_in'=>auth('api')->factory()->getTTL()*60,
        'user'=>auth('api')->user()]);
    }

    public function refresh() {
        try {
            $newToken = auth('api')->refresh();
            return response()->json([
                'access_token' => $newToken,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
            ]);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }
    }

    public function logout() {
        auth('api')->logout();
        return response()->json(['message'=>'Logged out']);
    }
}
