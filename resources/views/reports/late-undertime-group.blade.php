@extends('layouts.main')

@section('title', 'Reports')

@section('content')
<h1><i class="fa fa-file-text"></i> Late & Under Time (Group)</h1>

<hr class="my-4">

<form id="searchFiltersFrm">
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
    var dataTable = null;
    var searchBtn = $('#searchBtn');

    searchBtn.click(function() {
      var data = $('#searchFiltersFrm').serialize();
      if(dataTable) {
        dataTable.ajax.url("{{url('api/reports/late-undertime-group')}}?"+data);
        dataTable.ajax.reload();
      } else {
        dataTable = $('table').DataTable({
          'searching': false,
          'ajax': "{{url('api/reports/late-undertime-group')}}?"+data,
          'columns': [
            { 'data': 'biometric_id' },
            { 'data': 'name' },
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

    searchBtn.trigger('click');
  });
</script>
@endsection
