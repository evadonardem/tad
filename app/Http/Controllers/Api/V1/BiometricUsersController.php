<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;

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
        $users = $this->zk->getUser();

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

        $attributes = $request->only(['school_id', 'biometric_id', 'name']);
        $users = $this->zk->getUser();
        $newRecordId = 1;

        if(!empty($users)) {
          $recordIds = array_column($users, 'record_id');
          $newRecordId = ((int)max($recordIds)) + 1;
        }

        $this->zk->setUser($newRecordId, $attributes['biometric_id'], $attributes['name'], '', 0);
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
        //
    }

    /**
     * Flush biometrics.
     *
     * @return bool
     */
    public function flush()
    {
        $this->zk->clearUser();

        return response()->json([
          'message' => empty($this->zk->getUser()) ?
            'Successfully flushed biometrics.' : 'Failed to flush biometrics.'
        ]);
    }
}
