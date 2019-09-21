<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;
use Dingo\Api\Routing\Helpers;
use App\Models\AttendanceLog;
use Carbon\Carbon;

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
    public function index(Request $request)
    {
        if ($this->zk) {
            $this->zk->disableDevice();

            $users = $this->api->get('biometric/users');
            $users = $users['data'];
            $keys = [];

            $logs = $this->zk->getAttendance();
            foreach ($logs as $log) {
                $biometricId = $log['biometric_id'];
                $filteredUser = array_filter($users, function ($user) use ($biometricId) {
                    return $user['biometric_id'] == $biometricId;
                });
                $user = array_pop($filteredUser);

                AttendanceLog::create([
                  'biometric_id' => $log['biometric_id'],
                  'biometric_name' => $user['name'],
                  'biometric_timestamp' => $log['timestamp']
                ]);
            }

            $this->zk->clearAttendance();
            $this->zk->enableDevice();
            $this->zk->disconnect();
        }

        $biometricId = $request->input('biometric_id');
        $name = $request->input('name');
        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('start_date'))
          ->setTime(0, 0, 0)
          ->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('end_date'))
          ->setTime(23, 59, 59)
          ->format('Y-m-d H:i:s');

        $logsQry = AttendanceLog::whereBetween('biometric_timestamp', [$startDate, $endDate]);

        if ($biometricId) {
            $logsQry->where('biometric_id', '=', $biometricId);
        }

        if ($name) {
            $logsQry->where('biometric_name', 'like', '%' . $name . '%');
        }

        $logs = $logsQry->orderBy('biometric_timestamp', 'asc')->get();

        return response()->json(['data' => $logs]);
    }
}
