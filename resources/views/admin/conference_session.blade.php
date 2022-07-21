@extends('layouts.app')

@section('content')
    @php  /* @var $id */  @endphp
    @php  /* @var $session_conference */  @endphp
    @php  /* @var $default_session_duration */  @endphp
    <admin-conference-session
        :session-id="{{ $id }}"
        :default-session-duration="{{ $default_session_duration }}"
        :conference-id="{{ $session_conference['id'] }}"
        :session-list-link="'{{ route('admin_conference_session_list') }}'"
    />
@endsection
