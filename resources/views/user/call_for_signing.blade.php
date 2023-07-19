@extends('layouts.app')

@section('content')
    @php  /* @var $conference */  @endphp
    <call-for-signing :conference-id="'{{ $conference }}'" />
@endsection
