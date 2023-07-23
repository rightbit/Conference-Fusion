@extends('layouts.app')

@section('content')
    @php  /* @var $conference */  @endphp
    <call-for-panels :conference-id="'{{ $conference }}'" :user-id="{{ Auth::user()->id }}" />
@endsection
