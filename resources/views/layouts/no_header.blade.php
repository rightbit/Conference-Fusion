@php  /* @var $finish_setup */  @endphp
@php  /* @var $session_conference */  @endphp
@php  /* @var $conference_short_list */  @endphp
        <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{  $config_sitename ?? 'Scheduler' }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <script>
        /*to prevent Firefox FOUC, this must be here*/
        let FF_FOUC_FIX;
    </script>

    @yield('header_scripts')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom/custom.app.css') }}" rel="stylesheet">

    @yield('styles')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-dark bg-background-main shadow-sm">
        <div class="container">
            <span class="navbar-brand">
                {{ $conference->name }}
            </span>
        </div>
    </nav>

    <main class="py-4">
        <div id="app">
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
