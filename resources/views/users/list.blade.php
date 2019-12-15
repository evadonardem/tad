@extends('layouts.main')

@section('title', 'TAD Users')

@section('content')
<h1><i class="fa fa-users"></i> Biometric Users</h1>

<hr class="my-4">

<div class="row">
  <div class="col-md-12 pull-right">
    <button class="btn btn-primary" type="button" id="addNewUserBtn">
      <i class="fa fa-plus"></i> Add New User
    </button>
  </div>
</div>

<div class="row">

</div>

<hr class="my-4">

<div class="row">
  <div class="col-md-12">
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
</div>

<!-- Add/Edit Biometric User -->
<div class="modal fade" id="addEditBiometricUserModal" tabindex="-1" role="dialog" aria-labelledby="addEditBiometricUserModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="addEditUserFrm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
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
            <select class="form-control" id="role" name="role">
              <option value=""></option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary"></button>
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
            var editBtn = '<a href="#" class="edit btn btn-secondary" data-toggle="modal" data-target="#addEditBiometricUserModal" data-user-id="' + row.id + '" data-biometric-id="' + row.biometric_id + '" data-name="' + row.name + '" data-role="' + row.role + '"><i class="fa fa-edit"></i></a>';
            var deleteBtn = '<a href="#" class="delete btn btn-warning" data-toggle="modal" data-target="#deleteModal" data-user-id="' + row.id + '" data-biometric-id="' + row.biometric_id + '" data-name="' + row.name + '"><i class="fa fa-trash"></i></a>';

            return editBtn + ' ' + deleteBtn;
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
        allowClear: true,
        placeholder: 'Select Role',
        width: '100%'
      });

    });

    var modal = $('#addEditBiometricUserModal');
    var addNewUserBtn = $('#addNewUserBtn')

    addNewUserBtn.click(function() {
      modal.find('.modal-title').html('<i class="fa fa-user"></i> Add New User');
      modal.find('#biometricId').val('').prop('readonly', false);
      modal.find('#name').val('');
      modal.find('#role').val('').trigger('change');
      modal.find('.modal-footer .btn.btn-primary').text('Register').off().click(function () {
        var url = "{{url('api/biometric/users')}}?token=" + token;
        $.ajax({
            url: url,
            method: 'POST',
            data: $('#addEditUserFrm', modal).serialize(),
            beforeSend: function () {
            	modal.find('.is-invalid').each(function() {
                $(this).removeClass('is-invalid');
    	        });
            },
            success: function(response) {
              $('#addEditUserFrm', modal)[0].reset();
              dataTable.ajax.reload();
              modal.modal('hide');
            },
            error: function(xhr) {
              var data = xhr.responseJSON;
              if (data) {
                var errors = data.errors;
                for (key in errors) {
                  $('[name=' + key + ']', modal).addClass('is-invalid').next().text(errors[key][0]);
                }
              }
            }
         });
      });
      modal.modal('show');
    });

    $(document).on('click', '.edit', function(e) {
      e.preventDefault();
      var modal = $('#addEditBiometricUserModal');
      var userId = $(this).data('user-id');
      var biometricId = $(this).data('biometric-id');
      var name = $(this).data('name');
      var role = $(this).data('role');
      modal.find('.modal-title').html('<i class="fa fa-user"></i> Edit User');
      modal.find('#biometricId').val(biometricId).prop('readonly', true);
      modal.find('#name').val(name);
      modal.find('#role').val(role).trigger('change');
      modal.find('.modal-footer .btn.btn-primary').text('Update').off().click(function () {
        var url = "{{url('api/biometric/users')}}/" + userId + "?token=" + token;
        $.ajax({
            url: url,
            method: 'PATCH',
            data: $('#addEditUserFrm', modal).serialize(),
            beforeSend: function () {
            	modal.find('.is-invalid').each(function() {
                $(this).removeClass('is-invalid');
    	        });
            },
            success: function(response) {
              $('#addEditUserFrm', modal)[0].reset();
              dataTable.ajax.reload();
              modal.modal('hide');
            },
            error: function(xhr) {
              var data = xhr.responseJSON;
              if (data) {
                var errors = data.errors;
                for (key in errors) {
                  $('[name=' + key + ']', modal).addClass('is-invalid').next().text(errors[key][0]);
                }
              }
            }
         });
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
              dataTable.ajax.reload();
              deleteModal.modal('hide');
            }
         });
      });
    });

  });
</script>
@endsection
