<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Hostel') }} - @yield('title', 'Dashboard')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('admin-assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('admin-assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="{{ asset('admin-assets/vendor/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet">

    @stack('styles')

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ Auth::user()->role === 'student' ? route('student.dashboard') : route('warden.dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-home"></i>
                </div>
                <div class="sidebar-brand-text mx-3">{{ Auth::user()->role === 'student' ? 'Student Portal' : 'Hostel Admin' }}</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            @if(Auth::user()->role === 'student')
                <!-- Student Sidebar -->
                <li class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('student.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
                </li>
                <li class="nav-item {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('student.attendance') }}">
                        <i class="fas fa-fw fa-calendar-check"></i>
                        <span>Attendance</span></a>
                </li>
                <li class="nav-item {{ request()->routeIs('student.hostels.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('student.hostels.index') }}">
                        <i class="fas fa-fw fa-building"></i>
                        <span>Browse Hostel</span></a>
                </li>
                <!-- Fees Parent Menu -->
                @php
                    $feesActive = request()->routeIs('student.fees.*');
                @endphp
                <li class="nav-item has-sub">
                    <a href="#feesSubmenu" class="nav-link" data-toggle="collapse" role="button" aria-expanded="{{ $feesActive ? 'true' : 'false' }}" aria-controls="feesSubmenu">
                        <i class="fas fa-fw fa-rupee-sign"></i>
                        <span>Fees</span>
                    </a>
                    <ul class="submenu collapse{{ $feesActive ? ' show' : '' }}" id="feesSubmenu">
                        <li class="nav-item {{ request()->routeIs('student.fees.paid') ? 'active' : '' }}">
                            <a href="{{ route('student.fees.paid') }}" class="nav-link">Paid</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('student.fees.pending') ? 'active' : '' }}">
                            <a href="{{ route('student.fees.pending') }}" class="nav-link">Pending</a>
                        </li>
                    </ul>
                </li>
            @else
                <!-- Warden/Admin Sidebar -->
                <!-- Divider -->
                <hr class="sidebar-divider">
                <!-- Heading -->
                <div class="sidebar-heading">
                    Hostel Management
                </div>
                <li class="nav-item {{ request()->routeIs('warden.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('warden.dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
                </li>
                <li class="nav-item {{ request()->routeIs('warden.hostels.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('warden.hostels.index') }}">
                        <i class="fas fa-fw fa-building"></i>
                        <span>Hostels</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('warden.hostels.students') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('warden.hostels.students', Auth::user()->id) }}">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Students</span>
                    </a>
                </li>
                <!-- Attendance Parent Menu -->
                @php
                    $attendanceActive = request()->routeIs('warden.hostels_attendance_hostels') || request()->routeIs('warden.meals-attendance.*');
                @endphp
                <li class="nav-item has-sub">
                    <a href="#attendanceSubmenu" class="nav-link" data-toggle="collapse" role="button" aria-expanded="{{ $attendanceActive ? 'true' : 'false' }}" aria-controls="attendanceSubmenu">
                        <i class="fas fa-fw fa-check-square"></i>
                        <span>Attendance</span>
                    </a>
                    <ul class="submenu collapse{{ $attendanceActive ? ' show' : '' }}" id="attendanceSubmenu">
                        <li class="nav-item {{ request()->routeIs('warden.hostels_attendance_hostels') ? 'active' : '' }}">
                            <a href="{{ route('warden.hostels_attendance_hostels') }}" class="nav-link">Hostel Attendance</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('warden.meals-attendance.*') ? 'active' : '' }}">
                            <a href="{{ route('warden.meals-attendance.index') }}" class="nav-link">Meal Attendance</a>
                        </li>
                    </ul>
                </li>
                <!-- End Attendance Parent Menu -->
                <li class="nav-item {{ request()->routeIs('warden.manage-hostel.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('warden.manage-hostel.index') }}">
                        <i class="fas fa-fw fa-cogs"></i>
                        <span>Manage Hostel</span></a>
                </li>
                <li class="nav-item {{ request()->routeIs('warden.rooms.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('warden.rooms.index') }}">
                        <i class="fas fa-fw fa-bed"></i>
                        <span>Rooms</span></a>
                </li>
                <li class="nav-item {{ request()->routeIs('warden.room-allotment.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('warden.room-allotment.index') }}">
                        <i class="fas fa-fw fa-user-plus"></i>
                        <span>Room Allotment</span></a>
                </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                <img class="img-profile rounded-circle" src="{{ asset('admin-assets/img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ Auth::user()->role === 'student' ? route('student.profile.edit') : route('warden.profile.edit') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; {{ config('app.name', 'Laravel Hostel') }} {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
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

    <!-- DataTables JavaScript -->
    <script src="{{ asset('admin-assets/vendor/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('admin-assets/vendor/datatables/dataTables.bootstrap4.js') }}"></script>

    @stack('scripts')

    <!-- Toast Notification -->
    <div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 1rem; right: 1rem; z-index: 9999;">
        <div class="toast" id="sessionToast" data-delay="4000" style="min-width: 250px;">
            <div class="toast-header">
                <strong class="mr-auto">Notification</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                <span id="toastMessage"></span>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = $('#sessionToast');
            var toastMsg = '';
            @if(session('success'))
                toastMsg = @json(session('success'));
                toastEl.find('#toastMessage').text(toastMsg);
                toastEl.removeClass('bg-danger').addClass('bg-success');
                toastEl.toast('show');
            @elseif(session('error'))
                toastMsg = @json(session('error'));
                toastEl.find('#toastMessage').text(toastMsg);
                toastEl.removeClass('bg-success').addClass('bg-danger');
                toastEl.toast('show');
            @endif
        });
    </script>

</body>

</html> 