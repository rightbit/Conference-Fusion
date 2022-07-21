@extends('layouts.app')

@section('content')
    @php  /* @var $session_conference */  @endphp
    <admin-conference-session-list
        :conference-id="{{ $session_conference['id'] }}"
        :session-link="'{{ route('admin_conference_session', '') }}'"
    />
@endsection
