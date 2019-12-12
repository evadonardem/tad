@extends('layouts.main')

@section('title', 'TAD Users')

@section('content')
<h1><i class="fa fa-users"></i> Biometric Users</h1>

<hr class="my-4">

<div class="row">

</div>

<hr class="my-4">

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Biometric ID</th>
              <th scope="col">Current Role</th>
              <th scope="col">Name</th>
              <th></th>
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
          <div class="form-group">
            <label for="type">Role:</label>
            <select class="form-control" id="role" name="role"></select>
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
    var token = $.cookie('token');
    var userRoleSelect = $('select[name="role"]');
    var dataTable = $('table').DataTable({
      'ajax': "{{url('api/biometric/users')}}?token=" + token,
      'columns': [
        { 'data': 'biometric_id' },
        { 'data': 'role' },
        { 'data': 'name' },
        {
          'data': null,
          'render': function (data, type, row) {
            var deleteBtn = '<a href="#" class="delete btn btn-warning" data-toggle="modal" data-target="#deleteModal" data-user-id="' + row.id + '" data-biometric-id="' + row.biometric_id + '" data-name="' + row.name + '"><i class="fa fa-trash"></i></a>';

            return deleteBtn;
          }
        }
      ]
    });

    $.get("{{url('api/settings/roles')}}?token=" + token, function(response) {
      var data = response.data;
      var users = [];

      for(i in data) {
        var role = data[i];
        users.push({
          'id': role.id,
          'text': role.id
        });
      }

      userRoleSelect.select2({
        data: users,
        placeholder: 'Select user role'
      });

    });

    $(document).on('click', '.delete', function (e) {
      e.preventDefault();
      var deleteModal = $('#deleteModal');
      var userId = $(this).data('user-id');
      var biometricId = $(this).data('biometric-id');
      var name = $(this).data('name');
      deleteModal.find('.modal-title').text('Delete Biometric User');
      deleteModal.find('.modal-body').text('Are you sure to delete biometric user ' + biometricId + ' ' + name + '?');
      deleteModal.find('.modal-footer .btn.btn-primary').off().click(function () {
        var url = "{{url('api/biometric/users')}}/" + userId + '?token=' + token;
        $.ajax({
            url: url,
            method: 'DELETE',
            success: function(response) {
              newFrm[0].reset();
              dataTable.ajax.reload();
              deleteModal.modal('hide');
            }
         });
      });
    });

    var newFrm = $('#newUserFrm');
    var registerBtn = $('#registerBtn');
    registerBtn.click(function() {
      var url = "{{url('api/biometric/users')}}?token=" + token;
      var data = newFrm.serialize();
      $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function(response) {
          newFrm[0].reset();
          dataTable.ajax.reload();
        }
      });
    });
  });
</script>
@endsection
