<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ZKLib\ZKLibrary;
use Dingo\Api\Routing\Helpers;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Artisan;

class BiometricAttendanceController extends Controller
{
    use Helpers;

    private $zk = null;

    public function __construct()
    {
      Artisan::call('config::cache');
      Artisan::call('config::clear');
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
      if($this->zk) {
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
      }

      $biometricId = $request->input('biometric_id');
      $name = $request->input('name');
      $year = $request->input('year') ?: Carbon::now()->format('Y');
      $month = $request->input('month');

      $startDate = Carbon::createFromDate($year, $month)->startOfMonth();
      $endDate = Carbon::createFromDate($year, $month)->endOfMonth();

      $logsQry = AttendanceLog::where('biometric_timestamp', '>=', $startDate->format('Y-m-d H:i:s'))
        ->where('biometric_timestamp', '<=', $endDate->format('Y-m-d H:i:s'));

      if($biometricId) {
        $logsQry->where('biometric_id', '=', $biometricId);
      }

      if($name) {
        $logsQry->where('biometric_name', 'like', '%' . $name . '%');
      }

      $logs = $logsQry->get();

      return response()->json(['data' => $logs]);
    }
}
