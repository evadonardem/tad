<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;
use App\User;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Hash;

class BiometricUsersController extends Controller
{
    private $zk = null;

    public function __construct()
    {
        $this->zk = new ZKLibrary(env('DEVICE_IP'), env('DEVICE_PORT'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();
        $users->each(function ($user) {
          if ($user->types->count() > 0) {
            $user->type = $user->types->last()->type;
          } else {
            $user->type = null;
          }

        });

        return response()->json(['data' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->zk->connect();

        $attributes = $request->only([
            'biometric_id',
            'type',
            'name'
        ]);

        $users = $this->zk->getUser();
        $newRecordId = 1;

        if (!empty($users)) {
          $recordIds = array_column($users, 'record_id');
          $newRecordId = ((int)max($recordIds)) + 1;
        }

        $this->zk->setUser(
            $newRecordId,
            $attributes['biometric_id'],
            $attributes['name'],
            '',
            0
        );

        $this->zk->disconnect();

        $user = User::create([
          'biometric_id' => $attributes['biometric_id'],
          'name' => $attributes['name'],
          'password' => ''
        ]);

        $user->types()->create([
          'type' => $attributes['type']
        ]);

        return ($user)
          ? response()->noContent()
          : response()->json('Forbidden', 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->zk->connect();

        $deviceUsers = $this->zk->getUser();

        $storedUser = User::findOrFail($id);

        $filteredDeviceUsers = array_filter($deviceUsers, function ($deviceUser) use ($storedUser) {
            return $deviceUser['biometric_id'] == $storedUser->biometric_id;
        });

        $deviceUser = (count($filteredDeviceUsers) > 0) ? array_pop($filteredDeviceUsers) : null;

        if ($deviceUser) {
            $this->zk->deleteUser($deviceUser['record_id']);
            $storedUser->delete();
            AttendanceLog::where('biometric_id', '=', $storedUser->biometric_id)->delete();

            $this->zk->disconnect();

            return $storedUser;
        }

        response()->json('Forbidden', 403);
    }

    public function syncAdminUsers()
    {
        $this->zk->connect();

        $deviceUsers = $this->zk->getUser();

        $this->zk->disconnect();

        $deviceUsersAdmin = array_filter($deviceUsers, function($deviceUser) {
            return $deviceUser['role_id'] == 14;
        });

        $isSync = false;
        foreach($deviceUsersAdmin as $deviceUserAdmin) {
            $user = User::where('biometric_id', '=', $deviceUserAdmin['biometric_id'])->first();
            $user->password = Hash::make($deviceUserAdmin['password']);
            $isSync = $isSync || $user->save();
        }

        if ($isSync) {
            return response()->json(['message' => 'Successfully sync admin users.']);
        }

        return response()->json(['error' => 'No registered admin to be sync.'], 422);
    }

    public function syncAllUsers()
    {
        $this->zk->connect();

        $biometricUsers = $this->zk->getUser();

        $this->zk->disconnect();

        $usersCount = User::all()->count();
        if ($usersCount == 0) {
            foreach ($biometricUsers as $user) {
                User::create([
                    'biometric_id' => $user['biometric_id'],
                    'name' => $user['name'],
                    'password' => !empty($user['password']) ? Hash::make($user['password']) : ''
                ]);
            }

            return response()->json(['message' => 'Successfully sync all users.'], 422);
        }

        return response()->json(['error' => 'Cannot sync all users.'], 422);
    }
}
