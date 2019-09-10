@extends('layouts.main')

@section('title', 'Reports')

@section('content')
<h1><i class="fa fa-file-text"></i> Late & Under Time (Individual)</h1>

<hr class="my-4">

<form id="searchFiltersFrm">
  <input type="hidden" name="type" value="individual">
  <div class="row">
    <div class="col">
      <div class="form-group">
        <label for="">Biometric User</label>
        <select class="form-control" name="biometric_id">
          <option></option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="form-group">
        <label for="">Year</label>
        <input type="text" class="form-control" name="year" value="{{ $currentYear }}">
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <label for="">Month</label>
        <select class="form-control" name="month">
          @foreach($months as $key => $value)
          <option value="{{ $key }}" {{ $key == $currentMonth ? 'selected' : null }}>{{ $value }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="col">
      <div class="form-group">
        <label for="">Period</label>
        <select class="form-control" name="period">
          <option value="1">1st Half</option>
          <option value="2">2nd Half</option>
        </select>
      </div>
    </div>
  </div>
</form>

<hr class="my-4">

<div class="row">
  <div class="col">
    <table class="table table-striped">
      <thead>
        <tr>          
          <th scope="col">Date</th>
          <th scope="col">Expected Time-in</th>
          <th scope="col">Expected Time-out</th>
          <th scope="col">Time-in</th>
          <th scope="col">Time-out</th>
          <th scope="col">Late</th>
          <th scope="col">Under Time</th>
          <th scope="col">Total</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('custom-scripts')
<script type="text/javascript">
  $(function() {
    var biometricUserSelect = $('select[name="biometric_id"]');
    var dataTable = null;
    var searchBtn = $('#searchBtn');

    $.get("{{url('api/biometric/users')}}", function(response) {
      var data = response.data;
      var users = [];

      for(i in data) {
        var user = data[i];
        users.push({
          'id': user.biometric_id,
          'text': user.biometric_id + ' ' + user.name
        });
      }

      biometricUserSelect.select2({
        data: users,
        placeholder: 'Select biometric user'
      });

      biometricUserSelect.on('change.select2', function() {
        var data = $('#searchFiltersFrm').serialize();
        if(dataTable) {
          dataTable.ajax.url("{{url('api/reports/late-undertime')}}?"+data);
          dataTable.ajax.reload();
        } else {
          dataTable = $('table').DataTable({
            'searching': false,
            'ajax': "{{url('api/reports/late-undertime')}}?"+data,
            'columns': [
              { 'data': 'date' },
              { 'data': 'expected_time_in' },
              { 'data': 'expected_time_out' },
              { 'data': 'time_in' },
              { 'data': 'time_out' },
              { 'data': 'late_in_minutes' },
              { 'data': 'undertime_in_minutes' },
              { 'data': 'total_late_undertime_in_minutes' }
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
