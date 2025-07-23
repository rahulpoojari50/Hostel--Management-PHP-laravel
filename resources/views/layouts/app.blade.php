<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Hostel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Global Styles -->
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f4f6f8;
            color: #333;
        }

        .navbar-brand {
            font-weight: bold;
            color: #dd2476 !important;
        }

        .nav-link.active {
            font-weight: 600;
            color: #dd2476 !important;
        }

        header.bg-white.shadow {
            border-bottom: 2px solid #e0e0e0;
        }

        main {
            padding: 2rem 1rem;
        }

        /* Scrollbar style for better UX */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background: #dd2476;
            border-radius: 8px;
        }

        .btn-primary {
            background: linear-gradient(to right, #ff512f, #dd2476);
            border: none;
        }

        .btn-primary:hover {
            background: #dd2476;
        }

        footer {
            padding: 1rem;
            text-align: center;
            font-size: 0.9rem;
            background-color: #fff;
            color: #666;
            border-top: 1px solid #e5e5e5;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="font-sans antialiased">

    <div class="min-h-screen d-flex flex-column">
        <!-- Navigation Bar -->
        @include('layouts.navigation')

        <!-- Flash Message Modal -->
        @if(session('success') || session('error'))
        <div class="modal fade show" id="flashModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.3);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header {{ session('success') ? 'bg-success' : 'bg-danger' }} text-white">
                        <h5 class="modal-title">{{ session('success') ? 'Success' : 'Error' }}</h5>
                        <button type="button" class="btn-close" onclick="document.getElementById('flashModal').style.display='none';"></button>
                    </div>
                    <div class="modal-body">
                        {{ session('success') ?? session('error') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('flashModal').style.display='none';">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            setTimeout(function(){
                var modal = document.getElementById('flashModal');
                if(modal) modal.style.display = 'none';
            }, 3000);
        </script>
        @endif

        <!-- Page Header (Optional) -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="container py-4">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-grow-1 container">
            @yield('content')
        </main>

        <!-- Optional Footer -->
        <footer>
            &copy; {{ date('Y') }} Laravel Hostel System. Made with ❤️.
        </footer>
    </div>

</body>
</html>
