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
        <nav class="navbar navbar-expand-md navbar-dark {{ empty($finish_setup) ? 'bg-background-main' : 'bg-danger' }} shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{  $config_sitename ?? 'Scheduler' }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="{{ route('home') }}">Home</a>
                            </li>
                            {{-- Admin Section --}}
                            @can('view_admin', 'user')
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('Admin') }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li class="ps-2"><b>Conference Admin</b></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_user_list') }}">{{ __('Users') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_volunteer_list') }}">{{ __('Volunteers') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_conference_session_list') }}">{{ __('Sessions') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_schedule_board') }}">{{ __('Schedule') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_announcement_list') }}">{{ __('Announcements') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_sponsor_list') }}">{{ __('Sponsors') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_reports') }}">{{ __('Reports') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="ps-2"><b>Settings</b></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_conference_list') }}">{{ __('Conferences') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_track_list') }}">{{ __('Tracks') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_room_list') }}">{{ __('Rooms') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin_configuration') }}">{{ __('Site Configuration') }}</a></li>
                                </ul>
                            </li>
                                @if($finish_setup)
                                    <li class="nav-item bg-warning">
                                        <a class="nav-link text-dark" href="{{ route('admin_user_list') }}"><i class="bi bi-exclamation-triangle-fill"></i> You MUST update the default user from initial setup. Click here.</a>
                                    </li>
                                @endif
                            @endcan
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        {{-- Admin Section --}}
                        @can('view_admin', 'user')
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ $session_conference['short_name'] }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @foreach($conference_short_list as $conference)
                                        <a class="dropdown-item" href=" {{ @route('select-conference', ['id' => $conference['id']]) }}">
                                            {{ $conference['short_name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </li>

                        @endcan

                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif

                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        {{ __('Profile') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
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
