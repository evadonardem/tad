@extends('layouts.main')

@section('title', 'Settings')

@section('content')
<h1><i class="fa fa-cogs"></i> Settings</h1>

<hr class="my-4">

<div class="row">
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center">
          <i class="fa fa-clock-o"></i><br>
          Common Time Shifts
        </h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('settings/common-time-shifts') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center">
          <i class="fa fa-users"></i><br>
          User Roles
        </h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('settings/user-roles') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
