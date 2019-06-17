@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{route('tasks.update')}}">
                <div class="card bg-dark text-white">
                    <div class="card-header">Edit Task</div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter task name">
                        </div>
                        <div class="form-group">
                            <label for="command">Command</label>
                            <input type="text" class="form-control" id="command" placeholder="e.g. create:snapshots">
                        </div>
                        <div class="form-group">
                            <label for="args">Arguments (separate multiple arguments with spaces)</label>
                            <input type="text" class="form-control" id="args" placeholder="e.g. --tag=Hourly">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-dark btn-outline-primary">Update</button>
                        <a href="{{route('tasks.index')}}" class="btn btn-dark btn-outline-secondary">Cancel</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection