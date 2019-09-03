<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZKLib\ZKLib;
use ZKLib\User;

class TAD extends Controller
{
    private $zk = null;
    private $isConnected = false;

    public function __construct()
    {
      $this->zk = new ZKLib(env('DEVICE_IP'), env('DEVICE_PORT'));
      $this->isConnected = $this->zk->connect();
    }

    public function index()
    {
      if($this->isConnected) {
        $attendance = $this->zk->getAttendance();
      }
    }

    public function users()
    {
      if($this->isConnected) {
        $zkUsers = $this->zk->getUser();
        $zkUsersArr = [];
        foreach($zkUsers as $zkUser) {
          $zkUsersArr[] = [
            'recordId' => $zkUser->getRecordId(),
            'userId' => $zkUser->getUserId(),
            //'groupId' => $zkUser->getGroupId(),
            'name' => $zkUser->getName(),
            //'password' => $zkUser->getPassword(),
            //'role' => $zkUser->getRole(),
            //'cardNo' => $zkUser->getCardNo(),
            //'timeZone' => $zkUser->getTimeZone(),
          ];
        }

        return $zkUsersArr;
      }
    }

    public function clearUsers()
    {
      if($this->isConnected) {
        $this->zk->clearUsers();
      }
    }
}
