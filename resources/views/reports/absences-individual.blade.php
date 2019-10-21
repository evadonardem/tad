@extends('layouts.main')

@section('title', 'Reports')

@section('content')
<h1>
  <i class="fa fa-file-text"></i>
  Absences No Time-In/Out (Individual)
</h1>

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
          <div class="alert alert-block alert-info">
            <i class="fa fa-icon fa-info-circle"></i>
            By default dates fall in <em>SUNDAY</em> are excluded.
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
            <th scope="col">Date</th>
            <th scope="col">Expected Time-in</th>
            <th scope="col">Expected Time-out</th>
            <th scope="col">Time-in</th>
            <th scope="col">Time-out</th>
            <th scope="col">Late (min.)</th>
            <th scope="col">Under Time (min.)</th>
            <th scope="col">Total (min.)</th>
            <th scope="col">Reason</th>
            <th scope="col"></th>
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
          'text': user.biometric_id + ' ' + user.name  + ' (' + user.type + ')'
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
          var period = 'From ' +
            $('[name="start_date"]').val() +
            ' To ' + $('[name="end_date"]').val();

          return biometricUser + ' Absences - ' + period;
        };

        $(this).find('.is-invalid').each(function() {
          $(this).removeClass('is-invalid');
        });

        if(dataTable) {
          dataTable.ajax.url("{{url('api/reports/absences')}}?token=" + token + '&' + data);
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
                footer: true
              }
            ],
            'paging': false,
            'searching': false,
            'ordering': false,
            'ajax': {
              'url': "{{url('api/reports/absences')}}?token=" + token + '&' + data,
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
              { 'data': 'display_date' },
              { 'data': 'expected_time_in' },
              { 'data': 'expected_time_out' },
              { 'data': 'time_in' },
              { 'data': 'time_out' },
              { 'data': 'late_in_minutes', 'className': 'text-right' },
              { 'data': 'undertime_in_minutes', 'className': 'text-right' },
              { 'data': 'total_late_undertime_in_minutes', 'className': 'text-right' },
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
            ]
          });
        }

      });
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
