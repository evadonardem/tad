@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="row">
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center"><i class="fa fa-calendar"></i><br>Attendance Logs</h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/attendance-logs') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center"><i class="fa fa-file-text"></i><br>Generate Reports</h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/reports') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center"><i class="fa fa-users"></i><br>Biometric Users</h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/users') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center"><i class="fa fa-cogs"></i><br>General Settings</h1>
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
