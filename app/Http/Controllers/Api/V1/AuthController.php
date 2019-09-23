<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Routing\Helpers;

class AuthController extends Controller
{
    use Helpers;

    public function login()
    {
        $credentials = request(['biometric_id', 'password']);

        try {
          if (!$token = JWTAuth::attempt($credentials)) {
              return response()->json(['error' => 'Unauthorized'], 401);
          }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Couldn\'t create token'], 500);
        }


        return response()->json(compact('token'));
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        Auth::guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        $user = $this->auth->user();
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('token'));
    }

    public function me()
    {
        $user = $this->auth->user();

        return $user;
    }
}
