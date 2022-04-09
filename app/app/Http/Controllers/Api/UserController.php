<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    //

    public function register(Request $request)
    {
        try {

            $validator = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8'
            ]);

            if (!$validator) {
                return response(['message' => "Fill in the reequired field appropriately"]);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $accessToken = $user->createToken('authToken')->accessToken;

            return response(['user' => $user, 'message' => 'Registration Successful', 'token' => $accessToken]);
        } catch (\Exception $ex) {
            session()->flash('error', 'Unable to add new User. If problem persists please contact developers.');
        }
    }



    public function login(Request $request)
    {
        try {
            $loginData = $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            if (auth()->attempt($loginData)) {
                $accessToken = auth()->user()->createToken('authToken')->accessToken;

                return response(['message' => 'Successfully Logged in', 'user' => auth()->user(), 'token' => $accessToken]);
            } else {
                return Response(['message' => 'Invalid credentials']);
            }
            // $accessToken = auth()->user()->createToken('authToken')->accessToken;


        } catch (\Exception $ex) {
            session()->flash('error', 'Unable to add new User. If problem persists please contact developers.');
        }
    }
}
