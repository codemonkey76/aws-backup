@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <table class="table table-dark">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Task Name</th>
                        <th scope="col">Frequency</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td scope="row">{{$task->id}}</td>
                            <td>{{$task->name}}</td>
                            <td>{{$task->frequency}}</td>
                            <td>
                                <a href="{{route('tasks.edit')}}" class="btn btn-dark btn-outline-primary">Edit</a>
                                <button class="btn btn-dark btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">There are currently no tasks.</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <a href="{{route('tasks.create')}}" class="btn btn-dark btn-outline-primary">New Task</a>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
