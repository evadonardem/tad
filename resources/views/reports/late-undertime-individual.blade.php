@extends('layouts.main')

@section('title', 'Reports')

@section('content')
<h1><i class="fa fa-file-text"></i> Late & Under Time (Individual)</h1>

<hr class="my-4">

<div class="row">
  <div class="col">
    <form id="searchFiltersFrm">
      <input type="hidden" name="type" value="individual">
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
    <div class="search-result" style="display: none;">
      <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">Date</th>
            <th scope="col">Expected Time-in</th>
            <th scope="col">Expected Time-out</th>
            <th scope="col">Time-in</th>
            <th scope="col">Time-out</th>
            <th scope="col">Late (min.)</th>
            <th scope="col">Under Time (min.)</th>
            <th scope="col">Total (min.)</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
          <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th style="text-align: right;">Total (min.):</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
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
          'text': user.biometric_id + ' ' + user.name
        });
      }

      biometricUserSelect.select2({
        data: users,
        placeholder: 'Select biometric user'
      });

      searchFiltersForm.submit(function(e) {
        e.preventDefault();

        var biometricUser = biometricUserSelect.select2('data')[0].text;
        var exportTitle = 'ReportLateUndertimeIndividual';
        var data = $(this).serialize();

        $('div.search-result-loading', 'body').remove();
        $('div.search-result').hide().before('<div class="search-result-loading"><h4><i class="fa fa-spin fa-spinner"></i> Loading...</h4></div>');
        $('table').find('caption.biometric-user').remove();
        $('table').append('<caption class="biometric-user" style="caption-side: top">' + biometricUser + '</caption>');

        if(dataTable) {
          dataTable.ajax.url("{{url('api/reports/late-undertime')}}?token=" + token + '&' + data);
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
            'ajax': "{{url('api/reports/late-undertime')}}?token=" + token + '&' + data,
            'initComplete': function () {
              $('div.search-result').show();
              $('div.search-result-loading', 'body').remove();
            },
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
            'order': [[2, 'asc']],
            'footerCallback': function ( row, data, start, end, display ) {
              var api = this.api(), data;

              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                  return typeof i === 'string' ?
                      i.replace(/[\$,]/g, '')*1 :
                      typeof i === 'number' ?
                          i : 0;
              };

              // Total over all pages
              total = api
                  .column( 7 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );

              // Update footer
              $( api.column( 7 ).footer() ).html( total );
            }
          });
        }

      });
    });
  });
</script>
@endsection
