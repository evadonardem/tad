<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="{{asset('css/all.css')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
        <script type="text/javascript">
          var appName = '{{ config("app.name") }}';
          var appBaseUrl = '{{url("")}}';
          var apiBaseUrl = '{{url("api")}}';
        </script>
    </head>
    <body>
      <div id="app"></div>

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

              hours = hours < 10 ? String('0' + hours).slice(-2) : String(hours);
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
      <script src="{{asset('js/app.js')}}" ></script>
    </body>
</html>
