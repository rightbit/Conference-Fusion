@extends('layouts.app')

@section('content')
    @php $superAdmin = false; @endphp
    @can('admin', 'user')
        @php $superAdmin = true; @endphp
    @endcan
    @php $viewAdmin = false; @endphp
    @can('view_admin', 'user')
        @php $viewAdmin = true; @endphp
    @endcan
    @php  /* @var $id */  @endphp
    <user-profile
        :user-id="{{ $id }}"
        :current-user="{{ auth()->user()->id }}"
        :super-admin="{{ $superAdmin }}"
        :view-admin="{{ $viewAdmin }}"
        :permissions="{{ json_encode(config('site.permission_names')) }}" />
@endsection
