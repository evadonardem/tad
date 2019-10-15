@extends('layouts.main')

@section('title', 'Reports')

@section('content')
<h1><i class="fa fa-file-text"></i> Reports</h1>

<hr class="my-4">

<div class="row">
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center">
          <i class="fa fa-file-text"></i>
          <i class="fa fa-users"></i><br>
          Late & Under Time (Group)
        </h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/reports/late-undertime-group') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center">
          <i class="fa fa-file-text"></i>
          <i class="fa fa-user"></i><br>
          Late & Under Time (Individual)
        </h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/reports/late-undertime-individual') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center">
          <i class="fa fa-file-text"></i>
          <i class="fa fa-users"></i><br>
          Absences<br>
          No Time-In/Out<br>
          (Group)
        </h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/reports/absences-group') }}" role="button">Continue &raquo;</a>
        </p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center">
          <i class="fa fa-file-text"></i>
          <i class="fa fa-users"></i><br>
          Absences<br>
          No Time-In/Out<br>
          (Individual)
        </h1>
        <hr class="my-4">
        <p class="lead text-center">
          <a class="btn btn-primary btn-lg" href="{{ url('biometric/reports/absences-individual') }}" role="button">Continue &raquo;</a>
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
