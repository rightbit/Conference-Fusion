@extends('layouts.app')

@section('content')
    @php  /* @var $id */  @endphp
    <admin-conference-info :page-id="{{ $id }}" />
@endsection
