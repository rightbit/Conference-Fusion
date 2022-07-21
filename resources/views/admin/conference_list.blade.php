@extends('layouts.app')

@section('content')
    <admin-conference-list :conference-link="'{{ route('admin_conference_info', '') }}'" />
@endsection
