@extends('layouts.app')

@section('content')
    @php  /* @var $session_conference */  @endphp
    <admin-track-list
        :conference-id="'{{ $session_conference['id'] }}'"
        :conference-name="'{{ $session_conference['short_name'] }}'"
        :user-link="'{{ route('user_profile', '') }}'"
        :user-list-link="'{{ route('admin_user_list') }}'"
    />
@endsection
