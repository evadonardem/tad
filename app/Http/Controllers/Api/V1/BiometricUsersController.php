<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;
use App\User;
use App\Models\AttendanceLog;

class BiometricUsersController extends Controller
{
    private $zk = null;

    public function __construct()
    {
        $this->zk = new ZKLibrary(env('DEVICE_IP'), env('DEVICE_PORT'));
        $this->zk->connect();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($this->zk) {
            $this->zk->disableDevice();

            $biometricUsers = $this->zk->getUser();

            $this->zk->enableDevice();
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
            }
        }

        $users = User::orderBy('name', 'asc')->get();

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
        if ($this->zk) {
            $this->zk->disableDevice();

            $attributes = $request->only(['biometric_id', 'name']);
            $users = $this->zk->getUser();
            $newRecordId = 1;

            if (!empty($users)) {
                $recordIds = array_column($users, 'record_id');
                $newRecordId = ((int)max($recordIds)) + 1;
            }

            $this->zk->setUser($newRecordId, $attributes['biometric_id'], $attributes['name'], '', 0);

            $this->zk->enableDevice();
            $this->zk->disconnect();

            $user = User::create([
                'biometric_id' => $attributes['biometric_id'],
                'name' => $attributes['name'],
                'password' => ''
            ]);

            return ($user) ? response()->noContent() : response()->json('Forbidden', 403);
        }

        return response()->json('Forbidden', 403);
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
        if ($this->zk) {
            $this->zk->disableDevice();

            $deviceUsers = $this->zk->getUser();
            $storedUser = User::findOrFail($id);

            $filteredDeviceUsers = array_filter($deviceUsers, function ($deviceUser) use ($storedUser) {
                return $deviceUser['biometric_id'] == $storedUser->biometric_id;
            });

            $deviceUser = (count($filteredDeviceUsers) > 0) ? array_pop($filteredDeviceUsers) : null;

            $this->zk->enableDevice();

            if ($deviceUser) {
                $this->zk->deleteUser($deviceUser['record_id']);

                $this->zk->disconnect();

                $storedUser->delete();
                AttendanceLog::where('biometric_id', '=', $storedUser->biometric_id)->delete();

                return $storedUser;
            }
        }

        return response()->json('Forbidden', 403);
    }
}
