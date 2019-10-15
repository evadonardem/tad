@extends('layouts.main')

@section('title', 'Reports')

@section('content')
<h1><i class="fa fa-file-text"></i> Absences (Group)</h1>

<hr class="my-4">

<div class="row">
  <div class="col">
    <form id="searchFiltersFrm">
      <input type="hidden" name="type" value="group">
      <div class="card">
        <h5 class="card-header">Search Filters</h5>
        <div class="card-body">
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
          <div class="alert alert-block alert-info">
            <i class="fa fa-icon fa-info-circle"></i>
            By default dates fall in weekend are excluded.
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
    <div class="search-result" style="display: none;">
      <table class="table table-striped" style="width: 100%;">
        <thead>
          <tr>
            <th scope="col">Biometric ID</th>
            <th scope="col">Name</th>
            <th scope="col">Type</th>
            <th scope="col">Date</th>
            <th scope="col">Expected Time-in</th>
            <th scope="col">Expected Time-out</th>
            <th scope="col">Time-in</th>
            <th scope="col">Time-out</th>
            <th scope="col">Late (min.)</th>
            <th scope="col">Under Time (min.)</th>
            <th scope="col">Total (min.)</th>
            <th scope="col">Reason</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('custom-scripts')
<script type="text/javascript">
  $(function() {
    var token = $.cookie('token');
    var dataTable = null;
    var searchFiltersForm = $('#searchFiltersFrm');

    searchFiltersForm.submit(function(e) {
      e.preventDefault();
      var exportTitle = 'ReportLateUndertimeByGroup';
      var data = $(this).serialize();

      $('div.search-result-loading', 'body').remove();
      $('div.search-result').hide().before('<div class="search-result-loading"><h4><i class="fa fa-spin fa-spinner"></i> Loading...</h4></div>');

      if(dataTable) {
        dataTable.ajax.url("{{url('api/reports/absences')}}?token=" + token + '&' +data);
        dataTable.ajax.reload(function () {
          $('div.search-result').show();
          $('div.search-result-loading', 'body').remove();
        });
      } else {
        dataTable = $('table').DataTable({
          'dom': 'Bfrtip',
          'buttons': [
            { extend: 'copyHtml5', title: exportTitle, footer: true },
            { extend: 'excelHtml5', title: exportTitle, footer: true },
            { extend: 'csvHtml5', title: exportTitle, footer: true },
            { extend: 'pdfHtml5', title: exportTitle, footer: true }
          ],
          'paging': false,
          'searching': false,
          'ajax': "{{url('api/reports/absences')}}?token=" + token + '&' + data,
          'initComplete': function () {
            $('div.search-result').show();
            $('div.search-result-loading', 'body').remove();
          },
          'columns': [
            { 'data': 'biometric_id' },
            { 'data': 'name' },
            { 'data': 'type' },
            { 'data': 'date' },
            { 'data': 'expected_time_in' },
            { 'data': 'expected_time_out' },
            { 'data': 'time_in' },
            { 'data': 'time_out' },
            { 'data': 'late_in_minutes' },
            { 'data': 'undertime_in_minutes' },
            { 'data': 'total_late_undertime_in_minutes' },
            { 'data': 'reason' },
            {
              'data': null,
              'render': function (data, type, row) {
                var manualTimeInOutBtn = !row.time_in && !row.time_out
                  ? '<a href="#" class="manual-time-in-out btn btn-warning" data-toggle="modal" data-target="#manualTimeInOutModal" data-date="' + row.date + '" data-biometric-id="' + row.biometric_id + '" data-name="' + row.name + '"><i class="fa fa-clock-o"></i></a>'
                  : null;

                return manualTimeInOutBtn;
              }
            }
          ],
          'columnDefs': [
            { 'orderable': false, 'targets': [0, 1] }
          ],
          'order': [[2, 'asc']]
        });
      }
    });

    $(document).on('click', '.manual-time-in-out', function (e) {
      e.preventDefault();
      var modal = $('#manualTimeInOutModal');
      var date = $(this).data('date');
      var biometricId = $(this).data('biometric-id');
      var name = $(this).data('name');
      modal.find('.modal-title').text('Manual Time-In/Out');
      modal.find('.modal-body').find('.biometric-id').text(biometricId);
      modal.find('.modal-body').find('.name').text(name);
      modal.find('.modal-body').find('#logDate').val(date);
      modal.find('.modal-body').find('#timeIn').val('');
      modal.find('.modal-body').find('#timeOut').val('');
      modal.find('.modal-body').find('#reason').val('');
      modal.find('.is-invalid').each(function() {
        $(this).removeClass('is-invalid');
      });
      modal.find('.modal-footer .btn.btn-primary').off().click(function () {
        var url = "{{url('api/override/manual-attendance-logs')}}?token=" + token;
        $.ajax({
            url: url,
            method: 'POST',
            data: {
              biometric_id: biometricId,
              log_date: date,
              time_in: modal.find('.modal-body').find('#timeIn').val(),
              time_out: modal.find('.modal-body').find('#timeOut').val(),
              reason: modal.find('.modal-body').find('#reason').val()
            },
            beforeSend: function () {
            	modal.find('.is-invalid').each(function() {
                $(this).removeClass('is-invalid');
    	        });
            },
            success: function(response) {
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

  });
</script>
@endsection
