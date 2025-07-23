<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Hostel') }} - Welcome</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('admin-assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin-assets/css/sb-admin-2.min.css') }}" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome to {{ config('app.name', 'Laravel Hostel') }}</h1>
                                        <p class="text-gray-600 mb-4">Hostel Management System</p>
                                    </div>
                                    
                                    @auth
                                        <div class="text-center mb-4">
                                            <div class="alert alert-success">
                                                <i class="fas fa-user-check fa-2x mb-2"></i>
                                                <h5>Welcome back, {{ Auth::user()->name }}!</h5>
                                                <p>You are logged in as a {{ ucfirst(Auth::user()->role) }}</p>
                                            </div>
                                            
                                            @if(Auth::user()->role === 'warden')
                                                <a href="{{ route('warden.dashboard') }}" class="btn btn-primary btn-user btn-block">
                                                    <i class="fas fa-tachometer-alt"></i> Go to Warden Dashboard
                                                </a>
                                            @elseif(Auth::user()->role === 'student')
                                                <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-user btn-block">
                                                    <i class="fas fa-tachometer-alt"></i> Go to Student Dashboard
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center mb-4">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                <h5>Please log in to continue</h5>
                                                <p>Access your hostel management dashboard</p>
                                            </div>
                                            
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-user btn-block">
                                                <i class="fas fa-sign-in-alt"></i> Login
                                            </a>
                                            
                                            @if (Route::has('register'))
                                                <a href="{{ route('register') }}" class="btn btn-success btn-user btn-block">
                                                    <i class="fas fa-user-plus"></i> Register
                                                </a>
                                            @endif
                                        </div>
                                    @endauth
                                    
                                    <hr>
                                    <div class="text-center">
                                        <h6 class="text-gray-600 mb-3">System Features</h6>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <i class="fas fa-building fa-2x text-primary mb-2"></i>
                                                <p class="small">Hostel Management</p>
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-bed fa-2x text-success mb-2"></i>
                                                <p class="small">Room Booking</p>
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-utensils fa-2x text-warning mb-2"></i>
                                                <p class="small">Meal Services</p>
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                                <p class="small">Student Portal</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('admin-assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin-assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin-assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('admin-assets/js/sb-admin-2.min.js') }}"></script>

</body>

</html>
