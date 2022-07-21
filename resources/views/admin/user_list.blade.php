@extends('layouts.app')

@section('content')
    <admin-user-list :user-link="'{{ route('user_profile', '') }}'" />
@endsection
