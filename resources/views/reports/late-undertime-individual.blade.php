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
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>
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
            <th scope="col">Type</th>
            <th scope="col">Date</th>
            <th scope="col">Expected Time-in</th>
            <th scope="col">Expected Time-out</th>
            <th scope="col">Time-in</th>
            <th scope="col">Time-out</th>
            <th scope="col">Late (min.)</th>
            <th scope="col">Under Time (min.)</th>
            <th scope="col">Adjustment (min.)</th>
            <th scope="col">Total (min.)</th>
            <th scope="col">Reason</th>
            <th scope="col"></th>
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
            <th style="text-align: right;">Total (min.):</th>
            <th style="text-align: right;"></th>
            <th></th>
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

    $('select, input', searchFiltersForm).on('change', function (e) {
      if (dataTable) {
        dataTable.clear().draw();
      }
      $('div.search-result').hide();
    });

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

        var data = $(this).serialize();
        var filename = function () {
          var biometricUser = biometricUserSelect.select2('data')[0].text;
          var period = $('[name="start_date"]').val() + ' to ' + $('[name="end_date"]').val();
          return biometricUser + ' Late And Under Time - ' + period;
        };

        $(this).find('.is-invalid').each(function() {
          $(this).removeClass('is-invalid');
        });

        $('div.search-result-loading', 'body').remove();
        $('div.search-result').hide().before('<div class="search-result-loading"><h4><i class="fa fa-spin fa-spinner"></i> Loading...</h4></div>');

        if(dataTable) {
          dataTable.ajax.url("{{url('api/reports/late-undertime')}}?token=" + token + '&' + data);
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
              { 'data': 'type' },
              { 'data': 'date' },
              { 'data': 'expected_time_in' },
              { 'data': 'expected_time_out' },
              { 'data': 'time_in' },
              { 'data': 'time_out' },
              { 'data': 'late_in_minutes' },
              { 'data': 'undertime_in_minutes' },
              { 'data': 'adjustment_in_minutes' },
              { 'data': 'total_late_undertime_in_minutes' },
              { 'data': 'reason' },
              {
                'data': null,
                'render': function (data, type, row) {
                  var manualTimeInOutBtn = !row.is_adjusted
                    ? '<a href="#" ' +
                      'class="adjustment-late-undertime btn btn-warning" ' +
                      'data-toggle="modal" '+
                      'data-target="#adjustmentLateUndertimeModal" ' +
                      'data-date="' + row.date + '" ' +
                      'data-biometric-id="' + row.biometric_id + '" ' +
                      'data-name="' + row.name + '" ' +
                      'data-late-in-minutes="' + row.late_in_minutes + '" ' +
                      'data-undertime-in-minutes="' + row.undertime_in_minutes + '" ' +
                      'data-total-late-undertime-in-minutes="' + row.total_late_undertime_in_minutes + '">' +
                          '<i class="fa fa-clock-o"></i>' +
                      '</a>'
                    : null;

                  return manualTimeInOutBtn;
                }
              }
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
                  .column( 9 )
                  .data()
                  .reduce( function (a, b) {
                      return intVal(a) + intVal(b);
                  }, 0 );

              // Update footer
              $( api.column( 9 ).footer() ).html( total.toFixed(2) );
            }
          });
        }

      });
    });

    $(document).on('click', '.adjustment-late-undertime', function (e) {
      e.preventDefault();
      var modal = $('#adjustmentLateUndertimeModal');
      var biometricId = $(this).data('biometric-id');
      var name = $(this).data('name');
      var date = $(this).data('date');
      var lateInMinutes = $(this).data('late-in-minutes');
      var undertimeInMinutes = $(this).data('undertime-in-minutes');
      var totalLateUndertimeInMinutes = $(this).data('total-late-undertime-in-minutes');
      modal.find('.modal-title').text('Adjustment Late/Under Time');
      modal.find('.modal-body').find('.biometric-id').text(biometricId);
      modal.find('.modal-body').find('.name').text(name);
      modal.find('.modal-body').find('#logDate').val(date);
      modal.find('.modal-body').find('#lateInMinutes').val(lateInMinutes);
      modal.find('.modal-body').find('#undertimeInMinutes').val(undertimeInMinutes);
      modal.find('.modal-body').find('#adjustmentInMinutes').val('');
      modal.find('.modal-body').find('#totalLateUndertimeInMinutes').val(totalLateUndertimeInMinutes);
      modal.find('.modal-body').find('#reason').val('');
      modal.find('.is-invalid').each(function() {
        $(this).removeClass('is-invalid');
      });

      modal.find('.modal-body').find('#adjustmentInMinutes')
        .off()
        .on('change', function() {
            var _totalLateUndertimeInMinutes = +lateInMinutes + +undertimeInMinutes - +$(this).val();
            modal.find('.modal-body')
                .find('#totalLateUndertimeInMinutes')
                .val(_totalLateUndertimeInMinutes.toFixed(2));
        });

      modal.find('.modal-footer .btn.btn-primary').off().click(function () {
        var url = "{{url('api/override/adjustment-late-undertime')}}?token=" + token;
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                biometric_id: biometricId,
                log_date: date,
                adjustment_in_minutes: modal.find('.modal-body')
                    .find('#adjustmentInMinutes')
                    .val(),
                total_late_undertime_in_minutes: modal.find('.modal-body')
                    .find('#totalLateUndertimeInMinutes')
                    .val(),
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
