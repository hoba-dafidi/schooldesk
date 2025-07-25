<!DOCTYPE html>

<html class="light-style layout-menu-fixed" data-theme="theme-default" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel" data-template="vertical-menu-laravel-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | Sneat - HTML Laravel Free Admin Template </title>
    <meta name="description"
        content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
    <meta name="keywords"
        content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Canonical SEO -->
    <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <!-- DataTables Bootstrap 5 -->
    {{-- <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" /> --}}


    <!-- Include Styles -->
    @include('layouts/sections/styles')

    <!-- Include Scripts for customizer, helper, analytics, config -->
    @include('layouts/sections/scriptsIncludes')
</head>

<body>

    <!-- Layout Content -->
    @yield('layoutContent')
    <!--/ Layout Content -->



    <!-- Include Scripts -->
    @include('layouts/sections/scripts')
    <script>
        window.userId = {{ Auth::id() }};
        document.addEventListener('DOMContentLoaded', function() {
            const toastElList = [].slice.call(document.querySelectorAll('.toast'))
            toastElList.forEach(function(toastEl) {
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 6000, // auto-dismiss after 6 seconds
                    autohide: true
                })
                toast.show()
            })

            if (typeof Echo !== 'undefined' && window.userId) {
                console.log("here 1");
                console.log(userId);
                Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('Notification received:', notification);

                        let bgColor = {
                            success: "#28c76f",
                            warning: "#ff9f43",
                            danger: "#ea5455",
                            info: "#00cfe8"
                        } [notification.urgency || 'info'];

                        Toastify({
                            text: notification.message || "New notification received",
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: bgColor,
                            stopOnFocus: true,
                            close: true,
                            onClick: function() {
                                if (notification.data?.url) {
                                    window.location.href = notification.data.url;
                                }
                            }
                        }).showToast();
                    });

            }


        });
    </script>


</body>

</html>
