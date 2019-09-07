@extends('layouts.main')

@section('title', 'Attendance Logs')

@section('content')
<h1><i class="fa fa-calendar"></i> Attendance Logs</h1>

<hr class="my-4">

<form id="searchFiltersFrm">
  <div class="row">
    <div class="col-4">
      <div class="form-group">
        <label for="">Biometric ID</label>
        <input type="text" class="form-control" name="biometric_id">
      </div>
    </div>
    <div class="col-4">
      <div class="form-group">
        <label for="">Name</label>
        <input type="text" class="form-control" name="name">
      </div>
    </div>
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
  </div>
  <button type="button" class="btn btn-primary" id="searchBtn">Search</button>
</form>

<hr class="my-4">

<div class="row">
  <div class="col">
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
@endsection

@section('custom-scripts')
<script type="text/javascript">
  $(function() {
    var dataTable = null;
    var searchBtn = $('#searchBtn');

    searchBtn.click(function() {
      var data = $('#searchFiltersFrm').serialize();
      if(dataTable) {
        dataTable.ajax.url("{{url('api/biometric/attendance-logs')}}?"+data);
        dataTable.ajax.reload();
      } else {
        dataTable = $('table').DataTable({
          'searching': false,
          'ajax': "{{url('api/biometric/attendance-logs')}}?"+data,
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

    searchBtn.trigger('click');
  });
</script>
@endsection
