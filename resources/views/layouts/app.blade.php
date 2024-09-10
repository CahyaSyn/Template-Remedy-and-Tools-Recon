<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <link rel="stylesheet" href="{{ asset('dist/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/datatables/DataTables-1.13.3/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/datatables/Responsive-2.4.0/css/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/datatables/Buttons-2.3.5/css/buttons.bootstrap5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('dist/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}">
    @yield('styles')
    <style>
        /* * {
            border: 1px solid red;
        } */

        /* dt-button align-items-center */
        div.dt-buttons {
            float: left;
        }

        div.dataTables_wrapper div.dataTables_filter {
            padding: .2rem;
        }

        div.dataTables_wrapper div.dataTables_info {
            float: left;
            padding: 8px 0px 0px 0px;
            font-weight: 500;
        }
    </style>
</head>

<body class="hold-transition dark-mode sidebar-mini">
    <div class="wrapper">
        @include('partials.navbar')
        @include('partials.sidebar')

        <div class="content-wrapper">

            @yield('content')

        </div>
        @include('partials.footer')
    </div>


    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>

    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('dist/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('dist/datatables/DataTables-1.13.3/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dist/datatables/Responsive-2.4.0/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dist/datatables/Buttons-2.3.5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('dist/datatables/JSZip-2.5.0/jszip.min.js') }}"></script>
    <script src="{{ asset('dist/datatables/Buttons-2.3.5/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('dist/datatables/Buttons-2.3.5/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('dist/datatables/Buttons-2.3.5/js/buttons.print.min.js') }}"></script>

    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

    <script src="{{ asset('dist/js/toastr.min.js') }}"></script>
    <script src="{{ asset('dist/js/style.min.js') }}"></script>

    @yield('scripts')

    <script>
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });

        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' ||
                (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
                (e.ctrlKey && e.key === 'U')) {
                e.preventDefault();
            }
        });

        // Disable text selection and copying
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
        });

        document.addEventListener('copy', function(e) {
            e.preventDefault();
        });

        // Additional measures to prevent viewing source
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'S') { // Prevent Ctrl+S
                e.preventDefault();
            }
            if (e.ctrlKey && e.key === 'P') { // Prevent Ctrl+P
                e.preventDefault();
            }
        });

        // Prevent dragging
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });

        // Prevent printing
        window.onbeforeprint = function() {
            return false;
        };
    </script>
    <script>
        // session alert
        @if (Session::has('success'))
            toastr.success("{{ session('success') }}")
        @endif

        @if (Session::has('error'))
            toastr.error("{{ session('error') }}")
        @endif
    </script>
</body>

</html>
