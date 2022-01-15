<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $req){
        $fields = $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logIn(Request $req){
        $fields = $req->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

       //check Email
       $user = User::where('email', '=', $fields['email'])->first();

       //check password
       if(!$user || !Hash::check($fields['password'], $user->password)){
           return response([
               'message' => "Bad Attempt",
               'status' => 401
           ]);
       }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }


    public function logOut(Request $req){
        $req->auth()->user()->tokens()->delete();
        return response("logged out");
    }
}
