<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ZKLib\ZKLibrary;

class TAD extends Controller
{
    private $zk = null;

    public function __construct()
    {
      $this->zk = new ZKLibrary(env('DEVICE_IP'), env('DEVICE_PORT'));
      $this->zk->connect();
    }

    public function register()
    {
      //return $this->zk->getUserTemplateAll(2);
      //return $this->zk->startEnroll(1, 0);
      $this->zk->deleteUser(2);
    }

    public function users()
    {
      $users = $this->zk->getUser();
      $this->zk->disconnect();

      return view('users.list', ['users' => $users]);
    }

    public function attendance()
    {
      $attendance = $this->zk->getAttendance();

      return $attendance;
    }

    public function test()
    {
      $this->zk->testVoice();
      $deviceName = $this->zk->getDeviceName();

      $this->zk->disconnect();

      return response()->json([
        'device_name' => $deviceName
      ]);
    }
}
