<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;

class AuthController extends Controller
{
    public function Registration(Request $request){

        $validator = Validator::make($request -> all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if($validator -> fails()){
            return response()->json([
                'success' => false,
                'message' => "Have's Wrongs",
                'data' => $validator->errors()
            ]);
        }
        $input = $request -> all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] = $user -> createToken('auth_token') -> plainTextToken;
        $success['name'] = $user -> name;

        return response() -> json([
            'success' => true,
            'message' => "Register's Success",
            'data' => $success
        ]);
    }

    public function Login(Request $request){

        if(Auth::attempt(['email' => $request -> email,'password' => $request -> password])){
            $auth = Auth::user();
            $success['token'] = $auth -> createToken('auth_token') -> plainTextToken;
            $success['name'] = $auth -> name;
            $success['email'] = $auth -> email;
            $success['created_at'] = $auth -> created_at;
            $success['updated_at'] = $auth -> updated_at;
            return response() -> json([
                'success' => true,
                'message' => "Login's Success",
                'data' => $success,
            ]);
        } else {
            return response() -> json([
                'success' => false,
                'message' => "Login's Failed",
                'data' => null
            ]);
        }
    }
}
