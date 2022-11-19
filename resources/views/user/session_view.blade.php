@extends('layouts.app')

@section('content')
    @php  /* @var $conference_slug */  @endphp
    @php  /* @var $session_id */  @endphp

    <user-session-view
        :conference-slug="'{{ $conference_slug }}'"
        :session-id="{{ $session_id }}"
        :user-id="{{ Auth::user()->id }}"
        :contact-email="'{{ $config_contact_email }}'"
    />
@endsection
