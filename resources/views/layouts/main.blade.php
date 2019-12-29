<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.bootstrap4.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
        <script type="text/javascript">
          $(function() {
            var token = $.cookie('token');
            $.post('{{ url("api/me") }}?token=' + token, { }, function (response) {
              $('.signed-in-user').text('Hi! ' + response.name);
            }).fail(function () {
              location.replace('{{ url("login") }}');
            });

            $('.sign-out').click(function () {
              $.post('{{ url("api/logout") }}?token=' + token, { }, function () {
                location.replace('{{ url("login") }}');
              });
            });
          });
        </script>
        <style media="screen">
        /* Absolute Center Spinner */
        .loading {
          position: fixed;
          z-index: 999;
          height: 2em;
          width: 2em;
          overflow: visible;
          margin: auto;
          top: 0;
          left: 0;
          bottom: 0;
          right: 0;
        }

        /* Transparent Overlay */
        .loading:before {
          content: '';
          display: block;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0,0,0,0.3);
        }

        /* :not(:required) hides these rules from IE9 and below */
        .loading:not(:required) {
          /* hide "loading..." text */
          font: 0/0 a;
          color: transparent;
          text-shadow: none;
          background-color: transparent;
          border: 0;
        }

        .loading:not(:required):after {
          content: '';
          display: block;
          font-size: 10px;
          width: 1em;
          height: 1em;
          margin-top: -0.5em;
          -webkit-animation: spinner 1500ms infinite linear;
          -moz-animation: spinner 1500ms infinite linear;
          -ms-animation: spinner 1500ms infinite linear;
          -o-animation: spinner 1500ms infinite linear;
          animation: spinner 1500ms infinite linear;
          border-radius: 0.5em;
          -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
          box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        }

        /* Animation */
        @-webkit-keyframes spinner {
          0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
          }
          100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
          }
        }
        @-moz-keyframes spinner {
          0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
          }
          100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
          }
        }
        @-o-keyframes spinner {
          0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
          }
          100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
          }
        }
        @keyframes spinner {
          0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
          }
          100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
          }
        }
        </style>
    </head>
    <body>

      <nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav">
            <li class="nav-item {{ Request::is('/') ? 'active' : null }}">
              <a class="nav-link" href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Dashboard <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item {{ Request::is('biometric/attendance-logs') ? 'active' : null }}">
              <a class="nav-link" href="{{ url('biometric/attendance-logs') }}"><i class="fa fa-calendar"></i> Attendance Logs</a>
            </li>
            <li class="nav-item {{ Request::is('biometric/reports') ? 'active' : null }}">
              <a class="nav-link" href="{{ url('biometric/reports') }}"><i class="fa fa-file-text"></i> Reports</a>
            </li>
            <li class="nav-item {{ Request::is('biometric/users') ? 'active' : null }}">
              <a class="nav-link" href="{{ url('biometric/users') }}"><i class="fa fa-users"></i> Biometric Users</a>
            </li>
            <li class="nav-item {{ Request::is('settings') ? 'active' : null }}">
              <a class="nav-link" href="{{ url('settings') }}"><i class="fa fa-cogs"></i> Settings</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="signed-in-user"></span>
              </a>
              <div class="dropdown-menu" aria-labelledby="userDropdownMenuLink">
                <a class="dropdown-item sign-out" href="#">Sign-out</a>
              </div>
            </li>
          </ul>
        </div>
      </nav>

      <div class="container-fluid my-4">

          @yield('content')

          <!-- Loader -->
          <div class="loading" style="display: none;">Loading&#8230;</div>

          <!-- Delete Modal -->
          <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title"></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <div class="modal-body">
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                          <button type="button" class="btn btn-primary">Yes</button>
                      </div>
                  </div>
              </div>
          </div>

      <!-- Late Under Time Adjustment Modal -->
    	<div class="modal fade" id="adjustmentLateUndertimeModal" tabindex="-1" role="dialog" aria-labelledby="adjustmentLateUndertimeModalTitle" aria-hidden="true">
    	  <div class="modal-dialog modal-dialog-centered" role="document">
    	    <div class="modal-content">
            <div class="modal-header">
    	        <h5 class="modal-title"></h5>
    	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    	          <span aria-hidden="true">&times;</span>
    	        </button>
    	      </div>
    	      <div class="modal-body">
              <p><span class="biometric-id"></span> <span class="name"></span></p>
              <div class="form-group">
                <label for="logDate">Date:</label>
                <input
                    type="date"
                    class="form-control"
                    id="logDate"
                    name="log_date"
                    readonly>
              </div>
              <div class="form-group">
                <label for="late">Late (HH:MM:SS):</label>
                <input
                    type="text"
                    class="form-control"
                    id="late"
                    name="late"
                    readonly>
              </div>
              <div class="form-group">
                <label for="undertime">Under Time (HH:MM:SS):</label>
                <input
                    type="text"
                    class="form-control"
                    id="undertime"
                    name="undertime"
                    readonly>
              </div>
              <div class="form-group">
                <label for="adjustment">Adjustment (HH:MM:SS):</label>
                <input
                    type="text"
                    class="form-control"
                    id="adjustment"
                    name="adjustment">
                <div class="invalid-feedback"></div>
              </div>
              <div class="form-group">
                <label for="totalLateUndertime">Total (HH:MM:SS):</label>
                <input type="text"
                    class="form-control"
                    id="totalLateUndertime"
                    name="total_late_undertime"
                    readonly>
                <div class="invalid-feedback"></div>
              </div>
              <div class="form-group">
                <label for="reason">Reason</label>
                <textarea class="form-control" id="reason" name="reason"></textarea>
                <div class="invalid-feedback"></div>
              </div>
    	      </div>
    	      <div class="modal-footer">
    	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    	        <button type="button" class="btn btn-primary">Save</button>
    	      </div>
    	    </div>
    	  </div>
    	</div>

        <!-- Manual Time-in/out Modal -->
		<div class="modal fade" id="manualTimeInOutModal" tabindex="-1" role="dialog" aria-labelledby="manualTimeInOutModalTitle" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title"></h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
            <p><span class="biometric-id"></span> <span class="name"></span></p>
            <div class="form-group">
              <label for="logDate">Date:</label>
              <input type="date" class="form-control" id="logDate" name="log_date" readonly>
              <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
              <label for="timeIn">Time-In:</label>
              <input type="time" class="form-control" id="timeIn" name="time_in">
              <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
              <label for="timeOut">Time-Out:</label>
              <input type="time" class="form-control" id="timeOut" name="time_out">
              <div class="invalid-feedback"></div>
            </div>
            <div class="form-group">
              <label for="timeOut">Reason</label>
              <textarea class="form-control" id="reason" name="reason"></textarea>
              <div class="invalid-feedback"></div>
            </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		        <button type="button" class="btn btn-primary">Save</button>
		      </div>
		    </div>
		  </div>
		</div>
      </div>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
      <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.bootstrap4.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
      <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

      <script type="text/javascript">
        var TADHelper = {
          formatTimeDisplay: function ( seconds ) {
              var hours = Math.floor(seconds / 3600) > 0
                ? Math.floor(seconds / 3600)
                : 0;
              seconds -= hours * 3600;
              var minutes = Math.floor(seconds / 60) > 0
                ? Math.floor(seconds / 60)
                : 0;
              seconds -= minutes * 60;
              seconds = seconds > 0 ? seconds : 0;

              hours = String('0' + hours).slice(-2);
              minutes = String('0' + minutes).slice(-2);
              seconds = String('0' + seconds).slice(-2);

              return hours + ':' + minutes + ':' + seconds;
          },
          timeToSeconds: function ( time ) {
            var time = time.split(':');
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            return intVal(time[0]) * 3600
              + intVal(time[1]) * 60
              + intVal(time[2]);
          }
        };
      </script>

      @yield('custom-scripts')
    </body>
</html>
