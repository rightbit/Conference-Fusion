@extends('layouts.no_header')

@section('content')

    <div class="container text-center my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Oh no!</h2>
                    </div>
                    <div class="card-body">
                        <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                        <p class="lead">There was an error finding that session<br />Please go back to the schedule app and try again.</p>
                        <a href="https://ltue.net/schedule/" class="btn btn-primary"><i class="bi bi-box-arrow-up-right m-o"></i> Back to the schedule</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection