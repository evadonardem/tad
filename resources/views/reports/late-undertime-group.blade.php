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
            <div class="col-md-6">
              <div class="form-group">
                <label>Role</label>
                <select class="form-control" name="role_id">
                  <option value=""></option>
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
            <th scope="col">Biometric ID</th>
            <th scope="col">Name</th>
            <th scope="col">Type</th>
            <th scope="col">Date</th>
            <th scope="col">Expected Time-in</th>
            <th scope="col">Expected Time-out</th>
            <th scope="col">Time-in</th>
            <th scope="col">Time-out</th>
            <th scope="col">Late (HH:MM:SS)</th>
            <th scope="col">Under Time (HH:MM:SS)</th>
            <th scope="col">Adjustment (HH:MM:SS)</th>
            <th scope="col">Total (HH:MM:SS)</th>
            <th scope="col">Remarks</th>
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
            <th style="text-align: right;">Total:</th>
            <th style="text-align: right;"></th>
            <th style="text-align: right;"></th>
            <th style="text-align: right;"></th>
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
    var dataTable = null;
    var searchFiltersForm = $('#searchFiltersFrm');
    var userRoleSelect = $('select[name="role_id"]');

    $.get("{{url('api/settings/roles')}}?token=" + token, function(response) {
      var data = response.data;
      var users = [];
      for(i in data) {
        var role = data[i];
        users.push({
          'id': role.id,
          'text': role.id
        });
      }
      userRoleSelect.select2({
        allowClear: true,
        data: users,
        placeholder: 'Select Role'
      });
    });

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
        var period = 'From ' +
            $('[name="start_date"]').val() +
            ' To ' +
            $('[name="end_date"]').val();

        return 'Late And Under Time - ' + period;
      };

      $(this).find('.is-invalid').each(function() {
        $(this).removeClass('is-invalid');
      });

      if(dataTable) {
        dataTable.ajax.url("{{url('api/reports/late-undertime')}}?token=" + token + '&' +data);
        dataTable.ajax.reload();
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
              footer: true,
              orientation: 'landscape',
              pageSize: 'legal'
            }
          ],
          'paging': false,
          'searching': false,
          'ordering': false,
          'ajax': {
            'url': "{{url('api/reports/late-undertime')}}?token=" + token + '&' + data,
            'beforeSend': function () {
                $('div.search-result').hide();
                $('.loading').show();
            },
            'complete': function () {
                $('div.search-result').show();
                $('.loading').hide();
            },
            'error': function (xhr) {
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
              $('div.search-result').hide();
              $('.loading').hide();
            }
          },
          'columns': [
            { 'data': 'biometric_id' },
            { 'data': 'name' },
            { 'data': 'type' },
            { 'data': 'display_date' },
            { 'data': 'expected_time_in' },
            { 'data': 'expected_time_out' },
            { 'data': 'time_in' },
            { 'data': 'time_out' },
            { 'data': 'late', 'className': 'text-right' },
            { 'data': 'undertime', 'className': 'text-right' },
            { 'data': 'adjustment', 'className': 'text-right' },
            { 'data': 'total_late_undertime', 'className': 'text-right' },
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
                    'data-late="' + row.late + '" ' +
                    'data-undertime="' + row.undertime + '" ' +
                    'data-total-late-undertime="' + row.total_late_undertime + '">' +
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

            // Total late over all pages
            totalLate = api
                .column( 8 )
                .data()
                .map( function(time) {
                  return TADHelper.timeToSeconds(time);
                })
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Total under time over all pages
            totalUndertime = api
                .column( 9 )
                .data()
                .map( function(time) {
                  return TADHelper.timeToSeconds(time);
                })
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Total adjustment over all pages
            totalAdjustment = api
                .column( 10 )
                .data()
                .map( function(time) {
                  return TADHelper.timeToSeconds(time);
                })
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Total late / undertime over all pages
            totalLateUndertime = api
                .column( 11 )
                .data()
                .map( function(time) {
                  return TADHelper.timeToSeconds(time);
                })
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 8 ).footer() ).html(
              TADHelper.formatTimeDisplay(totalLate)
            );
            $( api.column( 9 ).footer() ).html(
              TADHelper.formatTimeDisplay(totalUndertime)
            );
            $( api.column( 10 ).footer() ).html(
              TADHelper.formatTimeDisplay(totalAdjustment)
            );
            $( api.column( 11 ).footer() ).html(
              TADHelper.formatTimeDisplay(totalLateUndertime)
            );
          }
        });
      }
    });

    $(document).on('click', '.adjustment-late-undertime', function (e) {
      e.preventDefault();
      var modal = $('#adjustmentLateUndertimeModal');
      var biometricId = $(this).data('biometric-id');
      var name = $(this).data('name');
      var date = $(this).data('date');
      var late = $(this).data('late');
      var undertime = $(this).data('undertime');
      var totalLateUndertime = $(this).data('total-late-undertime');
      modal.find('.modal-title').text('Adjustment Late/Under Time');
      modal.find('.modal-body').find('.biometric-id').text(biometricId);
      modal.find('.modal-body').find('.name').text(name);
      modal.find('.modal-body').find('#logDate').val(date);
      modal.find('.modal-body').find('#late').val(late);
      modal.find('.modal-body').find('#undertime').val(undertime);
      modal.find('.modal-body').find('#adjustment').val('');
      modal.find('.modal-body').find('#totalLateUndertime').val(totalLateUndertime);
      modal.find('.modal-body').find('#reason').val('');
      modal.find('.is-invalid').each(function() {
        $(this).removeClass('is-invalid');
      });

      modal.find('.modal-body').find('#adjustment')
        .off()
        .on('change', function() {
          var lateInSeconds = TADHelper.timeToSeconds(late);
          var undertimeInSeconds = TADHelper.timeToSeconds(undertime);
          var adjustmentInSeconds = TADHelper.timeToSeconds($(this).val());
          var _totalLateUndertimeInSeconds = +lateInSeconds
            + +undertimeInSeconds
            - +adjustmentInSeconds;

          if (_totalLateUndertimeInSeconds >= 0) {
            modal.find('.modal-body')
                .find('#totalLateUndertime')
                .val(TADHelper.formatTimeDisplay(_totalLateUndertimeInSeconds));
          } else {
            modal.find('.modal-body')
                .find('#totalLateUndertime')
                .val('');
          }
        });

      modal.find('.modal-footer .btn.btn-primary').off().click(function () {
        var url = "{{url('api/override/adjustment-late-undertime')}}?token=" + token;
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                biometric_id: biometricId,
                log_date: date,
                adjustment: modal.find('.modal-body')
                    .find('#adjustment')
                    .val(),
                total_late_undertime: modal.find('.modal-body')
                    .find('#totalLateUndertime')
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
