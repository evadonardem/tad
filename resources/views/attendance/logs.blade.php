@extends('layouts.main')

@section('title', 'Attendance Logs')

@section('content')
<h1><i class="fa fa-calendar"></i> Attendance Logs</h1>

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
    var dataTable = $('table').DataTable({
      'ajax': "{{url('api/biometric/attendance-logs')}}",
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
