<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Routing\Helpers;
use App\ZKLib\ZKLibrary;

class AuthController extends Controller
{
    use Helpers;

    private $zk = null;

    public function __construct()
    {
        $this->zk = new ZKLibrary(env('DEVICE_IP'), env('DEVICE_PORT'));
        $this->zk->connect();
    }

    public function login()
    {
        $isDeviceUserAdmin = false;
        $credentials = request(['biometric_id', 'password']);

        if ($this->zk) {
            $deviceUsers = $this->zk->getUser();
            $deviceUsersAdmin = array_filter($deviceUsers, function($deviceUser) use ($credentials) {
                return $deviceUser['role_id'] == 14 && $deviceUser['biometric_id'] == $credentials['biometric_id'];
            });
            
            $isDeviceUserAdmin = count($deviceUsersAdmin) > 0;
        }

        if ($isDeviceUserAdmin) {
            try {
              if (!$token = JWTAuth::attempt($credentials)) {
                  return response()->json(['error' => 'Unauthorized'], 401);
              }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Couldn\'t create token'], 500);
            }


            return response()->json(compact('token'));
        }

        return response()->json(['error' => 'Unauthorized'], 401);
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
