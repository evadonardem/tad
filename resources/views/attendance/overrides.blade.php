@extends('layouts.main')

@section('title', 'Attendance Logs')

@section('content')
<h1><i class="fa fa-calendar-plus-o"></i> Override Logs</h1>

<hr class="my-4">

<button class="create-override btn btn-primary"><i class="fa fa-plus"></i> Create Override</button>

<hr class="my-4">

<!-- Create Override -->
<div class="modal fade" id="createOverrideModal" tabindex="-1" role="dialog" aria-labelledby="createOverrideModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="createOverrideFrm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-calendar-plus-o"></i> Override</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="override_date">Date:</label>
            <input type="date" class="form-control" id="override_date" name="override_date">
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group">
            <label for="roles">Role:</label>
            <select class="form-control" id="roles" name="roles[]" multiple>
            </select>
            <div class="invalid-feedback"></div>
          </div>

          <!-- Override Expected Time-in and/or Out -->
          <div class="form-group">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="do_override_expected" name="do_override_expected" value="true">
              <label class="form-check-label" for="do_override_expected">Override Expected:</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="override_expected" id="override_expected_time_in_and_out" value="time_in_and_out" checked disabled>
              <label class="form-check-label" for="override_expected_time_in_and_out">Time-in and out</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="override_expected" id="override_expected_time_in_only" value="time_in_only" disabled>
              <label class="form-check-label" for="override_expected_time_in_only">Time-in only</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="override_expected" id="override_expected_time_out_only" value="time_out_only" disabled>
              <label class="form-check-label" for="override_expected_time_out_only">Time-out only</label>
            </div>
          </div>
          <div class="form-group" style="display: none;">
            <label for="override_time_in">Expected Time-in:</label>
            <input type="time" class="form-control" id="override_expected_time_in" name="override_expected_time_in" disabled>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group" style="display: none;">
            <label for="override_time_out">Expected Time-out:</label>
            <input type="time" class="form-control" id="override_expected_time_out" name="override_expected_time_out" disabled>
            <div class="invalid-feedback"></div>
          </div>

          <!-- Override Log Time-in and/or Out -->
          <div class="form-group">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="do_override_log">
              <label class="form-check-label" for="do_override_log">Override Log:</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="override_log" id="override_log_time_in_and_out" value="time_in_and_out" checked disabled>
              <label class="form-check-label" for="override_log_time_in_and_out">Time-in and out</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="override_log" id="override_log_time_in_only" value="time_in_only" disabled>
              <label class="form-check-label" for="override_log_time_in_only">Time-in only</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="override_log" id="override_log_time_out_only" value="time_out_only" disabled>
              <label class="form-check-label" for="override_log_time_out_only">Time-out only</label>
            </div>
          </div>
          <div class="form-group" style="display: none;">
            <label for="override_log_time_in">Log Time-in:</label>
            <input type="time" class="form-control" id="override_log_time_in" name="override_log_time_in" disabled>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group" style="display: none;">
            <label for="override_log_time_out">Log Time-out:</label>
            <input type="time" class="form-control" id="override_log_time_out" name="override_log_time_out" disabled>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-group" style="display: none;">
            <label for="override_time_in">Except users:</label>
            <select class="form-control" id="override_log_except_users" name="override_log_except_users" multiple disabled></select>
            <div class="invalid-feedback"></div>
          </div>

          <div class="form-group">
            <label for="override_reason">Reason:</label>
            <textarea class="form-control" id="override_reason" name="override_reason"></textarea>
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('custom-scripts')
<script type="text/javascript">
  $(function() {
    var token = $.cookie('token');
    var userRoleSelect = $('#roles');

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
        placeholder: 'Select Role',
        width: '100%'
      });
    });

    var createOverrideBtn = $('.create-override');
    var modal = $('#createOverrideModal');
    var form = $('#createOverrideFrm', modal);

    var overrideTypes = ['expected', 'log'];
    for (i in overrideTypes) {
      var type = overrideTypes[i];
      form.find('#do_override_' + type).change(function() {
        var checked = $(this).is(':checked');
        var target = $(this).prop('id').replace('do_', '');
        form.find('[name=' + target + ']').prop('disabled', !checked);
        form.find('[name=' + target + ']:checked').trigger('change');
      });
      form.find('[name=override_' + type + ']').change(function() {
        var overrideExpected = $(this).val();
        var isEnabled = !$(this).prop('disabled');
        var target = $(this).prop('name');

        if (isEnabled) {
          if (overrideExpected === 'time_in_and_out') {
            $('#' + target + '_time_in')
              .prop('disabled', false)
              .closest('.form-group')
              .show();
            $('#' + target + '_time_out')
              .prop('disabled', false)
              .closest('.form-group')
              .show();
          } else if (overrideExpected === 'time_in_only') {
            $('#' + target + '_time_in')
              .prop('disabled', false)
              .closest('.form-group')
              .show();
            $('#' + target + '_time_out')
              .prop('disabled', true)
              .closest('.form-group')
              .hide();
          } else {
            $('#' + target + '_time_in')
              .prop('disabled', true)
              .closest('.form-group')
              .hide();
            $('#' + target + '_time_out')
              .prop('disabled', false)
              .closest('.form-group')
              .show();
          }

          var biometricUserSelect = $('#override_log_except_users');
          $.get("{{url('api/biometric/users')}}?token=" + token, function(response) {
              var data = response.data;
              var users = [];

              for(i in data) {
                var user = data[i];
                users.push({
                  'id': user.biometric_id,
                  'text': user.biometric_id + ' ' + user.name + ' (' + user.role + ')'
                });
              }

              biometricUserSelect.select2({
                data: users,
                placeholder: 'Select biometric user'
              });
          });

          $('#' + target + '_except_users')
            .prop('disabled', false)
            .closest('.form-group')
            .show();

        } else {
          $('#' + target + '_time_in')
            .prop('disabled', true)
            .closest('.form-group')
            .hide();
          $('#' + target + '_time_out')
            .prop('disabled', true)
            .closest('.form-group')
            .hide();
          $('#' + target + '_except_users')
            .prop('disabled', true)
            .closest('.form-group')
            .hide();
        }
      });
    }

    form.off().submit(function (e) {
      e.preventDefault();

      var url = "{{url('api/override/attendance-logs')}}?token=" + token;
      $.ajax({
        url: url,
        method: 'POST',
        data: form.serialize(),
        beforeSend: function () {
          form.find('.is-invalid')
            .each(function() {
              $(this).removeClass('is-invalid');
            });
        },
        success: function(response) {
          form[0].reset();
          //userRolesDataTable.ajax.reload();
          modal.modal('hide');
        },
        error: function(xhr) {
          var data = xhr.responseJSON;
          if (data) {
            var errors = data.errors;
            for (key in errors) {
              $('[name=' + key + ']', modal)
                .addClass('is-invalid')
                .next()
                .text(errors[key][0]);
            }
          }
        }
     });
   });

    createOverrideBtn.click(function() {
      for (i in overrideTypes) {
        var type = overrideTypes[i];
        form.find('#do_override_' + type).trigger('change');
      }
      modal.find('#override_date').val('');
      modal.find('#roles').val('').trigger('change');
      modal.find('#override_log_except_users').val('').trigger('change');
      modal.modal('show');
    });
  });
</script>
@endsection
