@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="row">
  <div class="col-md">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center"><i class="fa fa-calendar"></i><br>Attendance Logs</h1>
        <p class="lead">Attendance logs fetched straight from biometric device.</p>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/attendance-logs') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center"><i class="fa fa-file-text"></i><br>Generate Reports</h1>
        <p class="lead">Generate reports from biometric attendance logs.</p>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/reports') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center"><i class="fa fa-users"></i><br>Biometric Users</h1>
        <p class="lead">View or register biometric device users.</p>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/users') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
</div>
<div class="row">
	<div class="col-md">
    <div class="jumbotron">
      <div class="container">
        <h1><i class="fa fa-cogs"></i> Settings</h1>
        <p class="lead"><i class="fa fa-clock-o"></i> Common expected time-in/out: <span class="badge badge-success">06:30 AM to 04:30 PM</span></p>
        <p class="lead"><i class="fa fa-users"></i> Users with active custom expected time-in/out:</p>
        <ul>
        	<li>20190001 Satur Cadsi <span class="badge badge-success">09:00 AM to 06:00 PM</span></li>
	        <li>20190001 Satur Cadsi <span class="badge badge-success">09:00 AM to 06:00 PM</span></li>
			<li>20190001 Satur Cadsi <span class="badge badge-success">09:00 AM to 06:00 PM</span></li>
        </ul>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('settings') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('custom-scripts')
<script type="text/javascript">

</script>
@endsection
