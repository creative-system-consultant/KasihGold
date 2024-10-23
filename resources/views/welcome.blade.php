<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('title')
        <title>@yield('title') - {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('img/kasihgoldicon.jpg')}}"> 

    <!-- Fonts -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    <!-- Styles -->
    @stack('before-styles')

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @livewireStyles
    <style>
        @media(max-width:1520px){
            .left-svg{
                display:none;
            }
        }
        html {
            scroll-behavior: smooth;
            background: #ffffff !important;
        }
        body {
            padding: 0rem !important;
        }
        /* small css for the mobile nav close */
        #nav-mobile-btn.close span:first-child{
            transform: rotate(45deg);
            top: 4px;
            position: relative;
            background:#a0aec0;
        }
        #nav-mobile-btn.close span:nth-child(2){
            transform: rotate(-45deg);
            margin-top: 0px;
            background:#a0aec0;
        }
    </style>

    <!-- Scripts -->
    {{-- <script src="{{ url(mix('js/app.js')) }}" defer></script> --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>

<body>
    <div class="overflow-x-hidden antialiased">
        <!-- Header Section -->
        @include('landing.header')

        <!-- BEGIN HERO SECTION -->
        @include('landing.home.hero')

        <!-- BEGIN FEATURES SECTION -->
        @include('landing.home.feature')

        <!-- Pricing Section -->
        @include('landing.home.pricing')

        <!-- Start advantage -->
        @include('landing.home.advantage')

        <!-- Start footer -->
        @include('landing.footer')

        <!-- a little JS for the mobile nav button -->
        <script>
            if( document.getElementById('nav-mobile-btn') ){
                document.getElementById('nav-mobile-btn').addEventListener('click', function(){
                    if( this.classList.contains('close') ){
                        document.getElementById('nav').classList.add('hidden');
                        this.classList.remove('close');
                    } else {
                        document.getElementById('nav').classList.remove('hidden');
                        this.classList.add('close');
                    }
                });
            }
        </script>
    </div>

    @livewireScripts
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            offset:100,
            duration :1000
        });
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
