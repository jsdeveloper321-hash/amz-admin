<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome (icons) -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            background: #f5f6fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 520px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: #fff;
        }

        .login-card img {
            max-width: 150px;
            margin-bottom: 15px;
        }

        .form-control {
            height: 45px;
        }

        .input-group-text {
            background: #fff;
            border-right: 0;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .btn-login {
            background: #e63946;
            color: #fff;
            height: 45px;
            border-radius: 25px;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #d62839;
        }

        .forgot {
            font-size: 13px;
        }

        @media(max-width:767px){
  .login-card {
            width: 95% !important;

        }
}
    </style>
</head>
<body>

<div class="login-card text-center">
    <!-- Logo -->

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif


    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">

    <h5 class="mb-4">Login</h5>
    <form action="{{ route('admin.login.post') }}" method="POST">
        @csrf
    <!-- Username -->
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-user text-muted"></i>
            </span>
        </div>
         <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
    </div>

    <!-- Password -->
    <div class="input-group mb-2">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-lock text-muted"></i>
            </span>
        </div>
       <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>

      <!-- User Type -->
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text">
                <i class="fas fa-user-tag text-muted"></i>
            </span>
        </div>
        <select name="type" class="form-control" required>
            <option value="">Select Type</option>
            <option value="SuperAdmin">Super Admin</option>
            <option value="Admin">Admin</option>
        </select>
    </div>


    <div class="text-right mb-3">
        <!--<a href="#" class="forgot">Forgot password?</a>-->
    </div>

    <!-- Login Button -->
    <button class="btn btn-login btn-block">Login</button>
</form>
</div>

</body>
</html>
