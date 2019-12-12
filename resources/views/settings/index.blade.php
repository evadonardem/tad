@extends('layouts.main')

@section('title', 'Settings')

@section('content')
<h1><i class="fa fa-cogs"></i> Settings</h1>

<hr class="my-4">

<h2><i class="fa fa-clock-o"></i> Common Time Shifts</h2>

<hr class="my-4">

<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Role</th>
              <th scope="col">Effectivity Date</th>
              <th scope="col">Expected Time-in</th>
              <th scope="col">Expected Time-out</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <form id="newCommonTimeShiftFrm">
      <div class="card">
        <h5 class="card-header">New Time Shift</h5>
        <div class="card-body">
          <div class="form-group">
            <label for="effectivityDate">Effectivity Date:</label>
            <input type="date" class="form-control" id="effectivityDate" name="effectivity_date" maxlength="10">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label for="expectedTimeIn">Expected Time-in:</label>
            <input type="time" class="form-control" id="expectedTimeIn" name="expected_time_in" maxlength="25">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label for="expectedTimeOut">Expected Time-out:</label>
            <input type="time" class="form-control" id="expectedTimeOut" name="expected_time_out" maxlength="25">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role_id" name="role_id"></select>
          </div>
          <button type="button" class="btn btn-primary btn-block" id="registerBtn" name="button">Register</button>
        </div>
      </div>
    </form>
  </div>
</div>

<hr class="my-4">

<h2><i class="fa fa-users"></i> User Types</h2>

<hr class="my-4">

@endsection

@section('custom-scripts')
<script type="text/javascript">
$(function() {
  var token = $.cookie('token');
	var dataTable = $('table').DataTable({
      'ajax': "{{url('api/settings/common-time-shifts')}}?token=" + token,
      'ordering': false,
      'searching': false,
      'columns': [
        { 'data': 'role_id' },
        { 'data': 'effectivity_date' },
        { 'data': 'expected_time_in' },
        { 'data': 'expected_time_out' },
        {
			'data': null,
			'render': function (data, type, row) {
				var deleteBtn = (row.effectivity_date && !row.is_locked)
					? '<a href="#" class="delete btn btn-warning" data-toggle="modal" data-target="#deleteModal" data-common-time-shift-id="' + row.id + '" data-effectivity-date="' + row.effectivity_date + '"><i class="fa fa-trash"></i></a>'
					: null;

				return deleteBtn;
			}
		},
      ]
    });

    var userRoleSelect = $('select[name="role_id"]');
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
		var commonTimeShiftId = $(this).data('common-time-shift-id');
		var effectivityDate = $(this).data('effectivity-date');
		deleteModal.find('.modal-title').text('Delete Time Shift');
		deleteModal.find('.modal-body').text('Are you sure to delete time shift for ' + effectivityDate + '?');
		deleteModal.find('.modal-footer .btn.btn-primary').off().click(function () {
			var url = "{{url('api/settings/common-time-shifts')}}/" + commonTimeShiftId + '?token=' + token;
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

    var newFrm = $('#newCommonTimeShiftFrm');
    var registerBtn = $('#registerBtn');
    registerBtn.click(function() {
      var url = "{{url('api/settings/common-time-shifts')}}?token=" + token;
      var data = newFrm.serialize();

      $.ajax({
        url: url,
        method: 'POST',
        data: data,
        beforeSend: function () {
        	newFrm.find('.is-invalid').each(function() {
		      $(this).removeClass('is-invalid');
	        });
        },
        success: function(response) {
          newFrm[0].reset();
          dataTable.ajax.reload();
        },
        error: function(xhr) {
          var data = xhr.responseJSON;
          if (data) {
            var errors = data.errors;
            for (key in errors) {
              $('[name=' + key + ']').addClass('is-invalid').next().text(errors[key][0]);
            }
          }
        }
      });
    });
});
</script>
@endsection
