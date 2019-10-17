<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;

class BiometricInfoController extends Controller
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
        $this->zk->connect();

        $this->zk->testVoice();

        $deviceName = $this->zk->getDeviceName();
        $deviceIP = $this->zk->ip;
        $devicePort = $this->zk->port;

        $this->zk->disconnect();

        return response()->json(['data' => [
          'device_name' => $deviceName,
          'device_ip' => $deviceIP,
          'device_port' => $devicePort
        ]
      ]);
    }
}
