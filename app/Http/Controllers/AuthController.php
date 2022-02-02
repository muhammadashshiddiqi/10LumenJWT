<?php 

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(HttpRequest $request){
        $field = $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required'
        ]);

        $user = new User();
        $user->name = $field['name'];
        $user->email = $field['email'];
        $user->password = Hash::make($field['password']);

        $user->save();

        return response()->json($user, 201);

    }

    public function login(HttpRequest $request){
        $field = $this->validate($request, [
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);

        $user = User::where('email', $field['email'])->first();

        if(!Hash::check($field['password'], $user->password)){
            return abort('401', 'email or password valid');
        }

        $payload = [
            'iat' => intval(microtime(true)),
            'exp' => intval(microtime(true)) + (60*60*1000),
            'uid' => $user->id
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');  

        return response()->json(['access_token' => $token ]);
    }
}
