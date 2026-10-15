@props(['subheading' => '', 'metadata' => '', 'metadescription' => '', 'tailwind' => '', 'bootstrap' => '', 'jQuery' => '', 'slickcarousel' => '', 'font2nd' => '', 'bIcons' => ''])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if($subheading){{ $subheading }} |@endif {{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('essentials/images/website-logo-main.png') }}" type="image/x-icon">
    <meta name="Csrf-token" content="{{ csrf_token() }}">
    @if(isset($metadata) && isset($metadescription))
    <meta name="{{ $metadescription }}" content="{{ $metadata }}">
    @endif
    @if($tailwind)
    <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @if($bootstrap)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    @endif
    @if($slickcarousel)
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @endif
    {{--figtree (google fonts)--}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    {{--figtree (google fonts)--}}
    @if($font2nd)
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @endif

    @if($bIcons)
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endif

    @yield('extracsscdns')

    {{--default css--}}
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #020618;
            color: hsl(0 0% 98%);
            font-family: "Figtree", sans-serif;
            font-optical-sizing: auto;
        }

        /* navbar css */
        header .main-web-side-logo {
            height: 15vh;
            width: 100%;
        }

        header .contact-section .bi-telephone-fill {
            background-color: #5404f5;
            padding-inline: .5rem;
            padding-block: .3rem;
        }

        .no-radius {
            border-radius: 0 !important;
        }

        header .navbar .dropdown-menu {
            margin-top: 2.1rem;
        }

        .font-500 {
            font-weight: 500;
        }

        .row {
            --bs-gutter-x: 0rem !important;
        }

        ul li {
            list-style: none;
        }

        /* navbar css */
        /* footer css */
        .website-logo {
            height: 5rem;
            width: auto;
        }

        footer .latest-updates-img {
            height: 3rem;
            width: 3rem;
            object-fit: cover;
        }

        /* footer css */
    </style>
    {{--default css--}}

    @if($font2nd)
    <style>
        .font-mont {
            font-family: "Montserrat", sans-serif;
            font-optical-sizing: auto;
        }
    </style>
    @endif

    @yield('extracss')

</head>

<body>
    {{ $slot }}
    @if($jQuery)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endif
    @if($slickcarousel)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js" integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endif

    @yield('extrajscdns')

    @if($bootstrap)
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    @endif

    {{---default js--}}
    <script>
        $(document).ready(function() {
            $(this).find('.scroll-top-direction').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 1000);
            });
        });
    </script>
    {{--default js--}}

    @yield('extrajs')
</body>

</html>