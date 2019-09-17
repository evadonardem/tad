@extends('layouts.main')

@section('title', 'TAD Users')

@section('content')
<h1><i class="fa fa-users"></i> Biometric Users</h1>

<hr class="my-4">

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Biometric ID</th>
              <th scope="col">Name</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <form id="newUserFrm">
      <div class="card">
        <h4 class="card-header"><i class="fa fa-user"></i> New User</h4>
        <div class="card-body">
          <div class="form-group">
            <label for="userId">Biometric ID: <small>Max 8 characters</small></label>
            <input type="text" class="form-control" id="biometricId" name="biometric_id" value="" maxlength="8">
          </div>
          <div class="form-group">
            <label for="name">Name: <small>Max 25 characters</small></label>
            <input type="text" class="form-control" id="name" name="name" value="" maxlength="25">
          </div>
          <button type="button" class="btn btn-primary btn-block" id="registerBtn" name="button">Register</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('custom-scripts')
<script type="text/javascript">
  $(function() {
    var dataTable = $('table').DataTable({
      'ajax': "{{url('api/biometric/users')}}",
      'columns': [
        { 'data': 'biometric_id' },
        { 'data': 'name' },
      ]
    });

    var newUserFrm = $('#newUserFrm');
    var registerBtn = $('#registerBtn');
    registerBtn.click(function() {
      var url = "{{url('api/biometric/users')}}"
      var data = newUserFrm.serialize();
      $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function(response) {
          newUserFrm[0].reset();
          dataTable.ajax.reload();
        }
      });
    });
  });
</script>
@endsection
