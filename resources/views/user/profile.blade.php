@extends('layouts.app')

@section('content')
    @php $superAdmin = false; @endphp
    @can('admin', 'user')
        @php $superAdmin = true; @endphp
    @endcan
    @php  /* @var $id */  @endphp
    <user-profile :user-id="{{ $id }}" :super-admin="{{ $superAdmin }}" :permissions="{{ json_encode(config('site.permission_names')) }}" />
@endsection
