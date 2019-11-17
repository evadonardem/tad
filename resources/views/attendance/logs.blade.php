@extends('layouts.main')

@section('title', 'Attendance Logs')

@section('content')
<h1><i class="fa fa-calendar"></i> Attendance Logs</h1>

<hr class="my-4">

<div class="row">
  <div class="col">
    <form id="searchFiltersFrm">
      <div class="card">
        <h5 class="card-header">Search Filters</h5>
        <div class="card-body">
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="">Biometric User</label>
                <select class="form-control" name="biometric_id">
                  <option></option>
                </select>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label>Start Date</label>
                <input type="date" class="form-control" name="start_date">
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label>End Date</label>
                <input type="date" class="form-control" name="end_date">
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="pull-right">
            <button type="submit" class="btn btn-primary">Search</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<hr class="my-4">

<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">Biometric ID</th>
              <th scope="col">Name</th>
              <th scope="col">Date Time</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('custom-scripts')
<script type="text/javascript">
  $(function() {
    var token = $.cookie('token');
    var biometricUserSelect = $('select[name="biometric_id"]');
    var dataTable = null;
    var searchFiltersForm = $('#searchFiltersFrm');

    $.get("{{url('api/biometric/users')}}?token=" + token, function(response) {
      var data = response.data;
      var users = [];

      for(i in data) {
        var user = data[i];
        users.push({
          'id': user.biometric_id,
          'text': user.biometric_id + ' ' + user.name  + ' (' + user.type + ')'
        });
      }

      biometricUserSelect.select2({
        allowClear: true,
        data: users,
        placeholder: 'Select biometric user'
      });

      searchFiltersForm.submit(function(e) {
        e.preventDefault();

        var data = $(this).serialize();
        if(dataTable) {
          dataTable.ajax.url("{{url('api/biometric/attendance-logs')}}?token=" + token + '&' + data);
          dataTable.ajax.reload();
        } else {
          dataTable = $('table').DataTable({
            'searching': false,
            'ajax': {
                'url': "{{url('api/biometric/attendance-logs')}}?token=" + token + '&' + data,
                'beforeSend': function () {
                    $('.loading').show();
                },
                'complete': function () {
                    $('.loading').hide();
                }
            },
            'columns': [
              { 'data': 'biometric_id' },
              { 'data': 'biometric_name' },
              { 'data': 'biometric_timestamp' }
            ],
            'columnDefs': [
              { 'orderable': false, 'targets': [0, 1] }
            ],
            'order': [[2, 'asc']]
          });
        }
      });

    });
  });
</script>
@endsection
