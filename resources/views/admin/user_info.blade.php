@extends('layouts.app')

@section('content')
    @php  /* @var $id */  @endphp
    <admin-user-info :user-id="{{ $id }}" />
@endsection
