@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-white">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <a href="{{route('tasks.index')}}" class="btn btn-dark btn-outline-primary">Tasks</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
