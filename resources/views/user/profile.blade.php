@extends('layouts.app')

@section('content')
    @php  /* @var $session_conference */  @endphp
    @php $superAdmin = 0; @endphp
    @can('admin', 'user')
        @php $superAdmin = 1; @endphp
    @endcan
    @php $viewAdmin = 0; @endphp
    @can('view_admin', 'user')
        @php $viewAdmin = 1; @endphp
    @endcan
    @php  /* @var $id */  @endphp
    <user-profile
        :user-id="{{ $id }}"
        :current-user="{{ auth()->user()->id }}"
        :super-admin="{{ $superAdmin }}"
        :view-admin="{{ $viewAdmin }}"
        :permissions="{{ json_encode(config('site.permission_names')) }}"
        :conference-id="'{{ $session_conference['id'] }}'"
    />
@endsection
