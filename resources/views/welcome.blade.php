@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
            <div class="col-md-12 p-lg-5 mx-auto my-5">
                <h1 class="display-4 font-weight-normal">{{ __('Welcome, Presenters') }}</h1>
                <p class="lead font-weight-normal">{!! $welcome_message !!}</p>
                @auth
                    <a class="btn btn-warning me-2" href="{{ route('home') }}">Go to your Dashboard</a>
                @endauth
                @guest
                    <a class="btn btn-outline-secondary me-2" href="{{ route('register')  }}">{{ __('Register') }}</a>
                    <a class="btn btn-outline-secondary" href="{{ route('login')  }}">{{ __('Login') }}</a>
                @endguest
            </div>
            <div class="product-device box-shadow d-none d-md-block"></div>
            <div class="product-device product-device-2 box-shadow d-none d-md-block"></div>
        </div>
    </div>
@endsection
