@extends('layouts.main')

@section('title', 'Settings')

@section('content')
<h1><i class="fa fa-users"></i> User Roles</h1>

<hr class="my-4">

<div class="row">
  <div class="col-md-12 pull-right">
    <button class="btn btn-primary" type="button" id="addNewUserRoleBtn">
      <i class="fa fa-plus"></i> Add New User Role
    </button>
  </div>
</div>

<hr class="my-4">

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <table id="userRolesList" class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Role</th>
              <th scope="col">Description</th>
              <th scope="col"></th>
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
<div class="modal fade" id="addEditUserRoleModal" tabindex="-1" role="dialog" aria-labelledby="addEditUserRoleModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="addEditUserRoleFrm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="effectivityDate">Title:</label>
            <input type="text" class="form-control" id="role_title" name="role_title">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label for="effectivityDate">Description:</label>
            <textarea class="form-control" id="role_description" name="role_description"></textarea>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"></button>
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
  var userRolesDataTable = $('table#userRolesList').DataTable({
    'ajax': "{{url('api/settings/roles')}}?token=" + token,
    'ordering': false,
    'searching': false,
    'columns': [
      { 'data': 'id'},
      { 'data': 'description' },
      { 'data': null,
        'render': function(data, type, row) {
          var actionButtons = $('<div class="btn-group" role="group" aria-label="actions"/>');
          var editButton = $('<button type="button" class="edit-role btn btn-primary" data-role-title="'+row.id+'" data-role-description="'+row.description+'"/>')
            .html('<i class="fa fa-edit"></i>');
          var deleteButton = $('<button type="button" class="delete-role btn btn-warning" data-role-id="'+row.id+'"/>')
            .html('<i class="fa fa-trash"></i>');

          actionButtons.append(editButton);
          actionButtons.append(deleteButton);

          return actionButtons.html();
        }
      }
    ]
  });

  var modal = $('#addEditUserRoleModal');
  var form = $('#addEditUserRoleFrm', modal);
  var addNewUserRoleBtn = $('#addNewUserRoleBtn');
  addNewUserRoleBtn.click(function() {
    modal.find('.modal-title')
      .html('<i class="fa fa-user"></i> Add New User Role');
    modal.find('#role_title').val('').prop('readonly', false);
    modal.find('#role_description').val('');
    modal.find('.modal-footer .btn.btn-primary').text('Add');

    form.find('.is-invalid').each(function() {
        $(this).removeClass('is-invalid');
    });
    form.off().submit(function (e) {
        e.preventDefault();

        var url = "{{url('api/settings/roles')}}?token=" + token;
        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            beforeSend: function () {
              form.find('.is-invalid')
                .each(function() {
                  $(this).removeClass('is-invalid');
                });
            },
            success: function(response) {
              form[0].reset();
              userRolesDataTable.ajax.reload();
              modal.modal('hide');
            },
            error: function(xhr) {
              var data = xhr.responseJSON;
              if (data) {
                var errors = data.errors;
                for (key in errors) {
                  $('[name=' + key + ']', modal)
                    .addClass('is-invalid')
                    .next()
                    .text(errors[key][0]);
                }
              }
            }
         });
       });
    modal.modal('show');
  });

  $(document).on('click', '.delete-role', function() {
    var roleId = $(this).data('role-id');
    var url = "{{url('api/settings/roles')}}/" + roleId + '?token=' + token;
    $.ajax({
          url: url,
          method: 'DELETE',
          success: function(response) {
            userRolesDataTable.ajax.reload();
          }
     });
  });

  $(document).on('click', '.edit-role', function() {
    var roleTitle = $(this).data('role-title');
    var roleDescription = $(this).data('role-description');

    modal.find('.modal-title')
      .html('<i class="fa fa-user"></i> Edit User Role');
    modal.find('#role_title').val(roleTitle).prop('readonly', true);
    modal.find('#role_description').val(roleDescription);
    modal.find('.modal-footer .btn.btn-primary').text('Update');

    form.find('.is-invalid').each(function() {
        $(this).removeClass('is-invalid');
    });
    form.off().submit(function (e) {
        e.preventDefault();

        var url = "{{url('api/settings/roles')}}/" + roleTitle + "?token=" + token;
        $.ajax({
            url: url,
            method: 'PATCH',
            data: form.serialize(),
            beforeSend: function () {
              form.find('.is-invalid')
                .each(function() {
                  $(this).removeClass('is-invalid');
                });
            },
            success: function(response) {
              form[0].reset();
              userRolesDataTable.ajax.reload();
              modal.modal('hide');
            },
            error: function(xhr) {
              var data = xhr.responseJSON;
              if (data) {
                var errors = data.errors;
                for (key in errors) {
                  $('[name=' + key + ']', modal)
                    .addClass('is-invalid')
                    .next()
                    .text(errors[key][0]);
                }
              }
            }
         });
       });

    modal.modal('show');
  });

});
</script>
@endsection
