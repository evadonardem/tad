<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;
use Artisan;

class BiometricInfoController extends Controller
{
    private $zk = null;

    public function __construct()
    {
      Artisan::call('config:cache');
      Artisan::call('config:clear');
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
      $this->zk->testVoice();

      $this->zk->disableDevice();

      $deviceName = $this->zk->getDeviceName();
      $deviceIP = $this->zk->ip;
      $devicePort = $this->zk->port;

      $this->zk->enableDevice();
      $this->zk->disconnect();

      return response()->json(['data' => [
          'device_name' => $deviceName,
          'device_ip' => $deviceIP,
          'device_port' => $devicePort
        ]
      ]);
    }
}
