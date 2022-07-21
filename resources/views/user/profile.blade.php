@extends('layouts.app')

@section('content')
    @php  /* @var $id */  @endphp
    <user-profile :user-id="{{ $id }}" />
@endsection
