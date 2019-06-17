@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <form method="POST" action="{{route('tasks.update', $task)}}">
                    @csrf
                    @method('PATCH')
                    <div class="card bg-dark text-white">
                        <div class="card-header">Edit Task</div>

                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control {{$errors->has('name')?'is-invalid':''}}" id="name" name="name" placeholder="Enter task name" value="{{old('name', $task->name)}}">
                                <div class="invalid-feedback">
                                    <span>{{$errors->first('name')}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="command">Command</label>
                                <input type="text" class="form-control {{$errors->has('command')?'is-invalid':''}}" id="command" name="command" placeholder="e.g. create:snapshots" value="{{old('command', $task->command)}}">
                                <div class="invalid-feedback">
                                    <span>{{$errors->first('command')}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="frequency">Frequency</label>
                                <select name="frequency" id="frequency" class="form-control {{$errors->has('frequency')?'is-invalid':''}}">
                                    <option>Select a frequency</option>
                                    @foreach(\App\Task::$frequencies as $frequency)
                                        <option value="{{$frequency}}" {{old('frequency', $task->frequency)===$frequency?'selected':''}}>{{preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", \Illuminate\Support\Str::Studly($frequency))}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    <span>{{$errors->first('frequency')}}</span>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="args">Arguments (separate multiple arguments with spaces)</label>
                                <input type="text" class="form-control {{$errors->has('args')?'is-invalid':''}}" id="args" name="args" placeholder="e.g. --tag=Hourly" value="{{old('args', $task->args)}}">
                                <div class="invalid-feedback">
                                    <span>{{$errors->first('args')}}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-dark btn-outline-primary">Save</button>
                            <a href="{{route('tasks.index')}}" class="btn btn-dark btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection