<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;
use Dingo\Api\Routing\Helpers;
use App\Models\AttendanceLog;

class BiometricAttendanceController extends Controller
{
    use Helpers;

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
        $this->zk->disableDevice();

        $users = $this->api->get('biometric/users');
        $users = $users['data'];
        $keys = [];

        $logs = $this->zk->getAttendance();
        foreach($logs as $log) {
          $biometricId = $log['biometric_id'];
          $filteredUser = array_filter($users, function($user) use ($biometricId) {
            return $user['biometric_id'] == $biometricId;
          });
          $user = array_pop($filteredUser);

          AttendanceLog::create([
            'biometric_record_id' => $user['record_id'],
            'biometric_id' => $log['biometric_id'],
            'biometric_name' => $user['name'],
            'biometric_timestamp' => $log['timestamp']
          ]);
        }

        $this->zk->clearAttendance();
        $this->zk->enableDevice();
        $this->zk->disconnect();

        $logs = AttendanceLog::all();

        return response()->json(['data' => $logs]);
    }
}
