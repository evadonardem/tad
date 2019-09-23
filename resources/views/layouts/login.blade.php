<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} Sign-in</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style media="screen">
        html,
        body {
          height: 100%;
        }
        body {
          display: -ms-flexbox;
          display: -webkit-box;
          display: flex;
          -ms-flex-align: center;
          -ms-flex-pack: center;
          -webkit-box-align: center;
          align-items: center;
          -webkit-box-pack: center;
          justify-content: center;
          padding-top: 40px;
          padding-bottom: 40px;
          background-color: #f5f5f5;
        }
        .form-signin {
          width: 100%;
          max-width: 330px;
          padding: 15px;
          margin: 0 auto;
        }
        .form-signin .checkbox {
          font-weight: 400;
        }
        .form-signin .form-control {
          position: relative;
          box-sizing: border-box;
          height: auto;
          padding: 10px;
          font-size: 16px;
        }
        .form-signin .form-control:focus {
          z-index: 2;
        }
        .form-signin input[type="email"] {
          margin-bottom: -1px;
          border-bottom-right-radius: 0;
          border-bottom-left-radius: 0;
        }
        .form-signin input[type="password"] {
          margin-bottom: 10px;
          border-top-left-radius: 0;
          border-top-right-radius: 0;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>

    <script type="text/javascript">
      $(function () {
          var token = $.cookie('token');
          $.post('{{ url("api/me") }}?token='+token, { }, function (response) {
            location.replace('{{ url("/") }}');
          });

          var signInFrm = $('.form-signin');
          signInFrm.submit(function (e) {
            e.preventDefault();
            var data  = $(this).serialize();
            $.ajax({
              url: '{{ url("api/login") }}',
              data: data,
              method: 'post',
              success: function (response) {
                var token = response.token;
                $.cookie('token', token);
                location.replace('{{ url("/") }}');
              },
              error: function() {
                signInFrm[0].reset();
              }
            });
          });
      });
    </script>
</head>

<body class="text-center">
    <form class="form-signin">
        <h1 class="h3 mb-3 font-weight-normal">
          <i class="fa fa-5x fa-user"></i><br>
          {{ env('APP_NAME') }}
        </h1>
        <p>Please sign in</p>
        <div class="form-group">
          <label for="inputBiometricId" class="sr-only">Biometric ID</label>
          <input type="text" id="inputBiometricId" name="biometric_id" class="form-control" placeholder="Biometric ID" autofocus>
        </div>
        <div class="form-group">
          <label for="inputPassword" class="sr-only">Password</label>
          <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password">
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-5 mb-3 text-muted">&copy; {{ date('Y') }}</p>
    </form>
</body>

</html>
