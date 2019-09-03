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

    public function users()
    {
      $users = $this->zk->getUser();

      $this->zk->disconnect();

      return $users;
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
