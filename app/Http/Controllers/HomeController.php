<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function login(Request $request){
        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response([
                'message' => 'Maaf email tidak terdaftar!'
            ], 401);
        }else if(!Hash::check($request->password, $user->password)){
            return response([
                'message' => 'Password salah!'
            ], 422);
        }
        $token = $user->createToken('user-token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
            'isLogin' => true
        ];

        return response($response, 200);
    }

    public function register(Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'bail|required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'required|min:5'
        ]);

        if($validate->fails()){
           return response([
               'message' => $validate->errors()->first()
           ], 402);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response($request, 201);
    }

    public function users(){
        $user = User::all();
        return response([
            'user' => $user
        ], 200);
    }

    public function logout(Request $request){
        $user = User::where('email', $request->email)->first();
        $user->tokens()->delete();
        return response([
            'message' => 'Logout Berhasil!',
            'status' => true
        ], 200);
    }
}
