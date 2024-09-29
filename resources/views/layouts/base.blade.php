<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @hasSection('title')

            <title>@yield('title') - {{ config('app.name') }}</title>
        @else
            <title>{{ config('app.name') }}</title>
        @endif

        <!-- tooltip -->
        <script src="{{ asset('js/popper.min.js')}}"></script>
        <script src="{{ asset('js/tippy.min.js')}}"></script>

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{asset('img/kasihgoldicon.jpg')}}">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ url(asset('css/app.css')) }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        @livewireStyles

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
        <script src="{{ url(mix('js/app.js')) }}" defer></script>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/aos.css')}}" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>

    <body>
        <style>
            .inputNumber::-webkit-outer-spin-button,
            .inputNumber::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
            }
            .inputNumber {
            -moz-appearance: textfield;
            }
        </style>
        @yield('body')

        @livewireScripts
        <script src="{{ asset('js/aos.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            AOS.init({
                offset:100,
                duration :1000
            });
        </script>
        <script>
            tippy('.tooltipbtn', {
                content:(reference)=>reference.getAttribute('data-title'),
                onMount(instance) {
                    instance.popperInstance.setOptions({
                    placement :instance.reference.getAttribute('data-placement')
                    });
                },
                allowHTML: true,
            });
        </script>
    </body>
</html>
