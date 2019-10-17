@extends('layouts.main')

@section('title', 'Reports')

@section('content')
<h1><i class="fa fa-file-text"></i> Late & Under Time (Group)</h1>

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
                <div class="invalid-feedback"></div>
              </div>
            </div>
            <div class="col">
              <div class="form-group">
                <label>End Date</label>
                <input type="date" class="form-control" name="end_date">
                <div class="invalid-feedback"></div>
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
            <th></th>
            <th></th>
            <th></th>
            <th style="text-align: right;">Total (min.):</th>
            <th style="text-align: right;"></th>
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
    var dataTable = null;
    var searchFiltersForm = $('#searchFiltersFrm');

    $('select, input', searchFiltersForm).on('change', function (e) {
      if (dataTable) {
        dataTable.clear().draw();
      }
      $('div.search-result').hide();
    });

    searchFiltersForm.submit(function(e) {
      e.preventDefault();

      var data = $(this).serialize();
      var filename = function () {
        var period = $('[name="start_date"]').val() + ' to ' + $('[name="end_date"]').val();
        return 'Late And Under Time - ' + period;
      };

      $(this).find('.is-invalid').each(function() {
        $(this).removeClass('is-invalid');
      });

      $('div.search-result-loading', 'body').remove();
      $('div.search-result').hide().before('<div class="search-result-loading"><h4><i class="fa fa-spin fa-spinner"></i> Loading...</h4></div>');

      if(dataTable) {
        dataTable.ajax.url("{{url('api/reports/late-undertime')}}?token=" + token + '&' +data);
        dataTable.ajax.reload(function () {
          $('div.search-result').show();
          $('div.search-result-loading', 'body').remove();
        });
      } else {
        $.fn.dataTable.ext.errMode = 'none';
        dataTable = $('table').DataTable({
          'dom': 'Bfrtip',
          'buttons': [
            {
              extend: 'excelHtml5',
              title: function() {
                return filename();
              },
              filename: function() {
                return filename();
              },
              footer: true
            },
            {
              extend: 'csvHtml5',
              filename: function() {
                return filename();
              },
              footer: true
            },
            {
              extend: 'pdfHtml5',
              title: function() {
                return filename();
              },
              filename: function() {
                return filename();
              },
              footer: true
            }
          ],
          'paging': false,
          'searching': false,
          'ordering': false,
          'ajax': {
            'url': "{{url('api/reports/late-undertime')}}?token=" + token + '&' + data,
            'error': function (xhr) {
              $('div.search-result-loading', 'body').remove();
              var data = xhr.responseJSON;
              if (data) {
                var errors = data.errors;
                for (key in errors) {
                  $('[name=' + key + ']', searchFiltersForm)
                    .addClass('is-invalid')
                    .closest('.form-group')
                    .find('.invalid-feedback')
                    .text(errors[key][0]);
                }
              }
            }
          },
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
            { 'data': 'total_late_undertime_in_minutes' }
          ],
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
                .column( 10 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 10 ).footer() ).html( total );
          }
        });
      }
    });

  });
</script>
@endsection
