@extends('layouts.app')

@section('content')
    <admin-volunteer-list :user-link="'{{ route('user_profile', '') }}'" />
@endsection
