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
              <th scope="col">Effectivity Date</th>
              <th scope="col">Expected Time-in</th>
              <th scope="col">Expected Time-out</th>
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
            <input type="text" class="form-control" id="effectivityDate" name="effectivity_date" maxlength="10">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label for="expectedTimeIn">Expected Time-in:</label>
            <input type="text" class="form-control" id="expectedTimeIn" name="expected_time_in" maxlength="25">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label for="expectedTimeOut">Expected Time-out:</label>
            <input type="text" class="form-control" id="expectedTimeOut" name="expected_time_out" maxlength="25">
            <div class="invalid-feedback"></div>
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
      'ajax': "{{url('api/settings/common-time-shifts')}}",
      'orderable': false,
      'order': [[0, 'desc']],
      'columns': [
        { 'data': 'effectivity_date' },
        { 'data': 'expected_time_in' },
        { 'data': 'expected_time_out' },
      ]
    });
    var newFrm = $('#newCommonTimeShiftFrm');
    var registerBtn = $('#registerBtn');
    registerBtn.click(function() {
      var url = "{{url('api/settings/common-time-shifts')}}"
      var data = newFrm.serialize();
      $.ajax({
        url: url,
        method: 'POST',
        data: data,
        success: function(response) {
          newFrm[0].reset();
          dataTable.ajax.reload();
        },
        error: function(xhr) {
          var data = xhr.responseJSON;
          if (data) {
            newFrm.find('.is-invalid').each(function() {
              $(this).removeClass('.is-invalid');
            });

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
