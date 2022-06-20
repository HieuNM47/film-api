<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct(Request $request)
    {

       $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {

        $validation = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validation->fails()){
            return $validation->errors();
        }

        $credentials = $request->only('email', 'password');

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);

    }

    public function register(Request $request){
        $validation = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validation->fails()){
            return $validation->errors();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'id_permission' => 1,
            'password' => Hash::make($request->password),
        ]);

        return $this->login($request);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    public function me()
    {
        return response()->json(auth()->user());
    }

    protected function respondWithToken($token)
    {
        return [
            'status' => 1,
            'message'=> 'success',
            'data' => [
                'profile'=> auth()->user(),
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ];
    }

}
