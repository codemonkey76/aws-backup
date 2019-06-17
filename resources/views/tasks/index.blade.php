@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <table class="table table-dark">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Task Name</th>
                        <th scope="col">Command</th>
                        <th scope="col">Arguments</th>
                        <th scope="col">Frequency</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td scope="row">{{$task->id}}</td>
                            <td>{{$task->name}}</td>
                            <td>{{$task->command}}</td>
                            <td>
                            <?php $args = explode(' ', $task->args) ?>
                            @foreach ($args as $arg)
                                    {{ $arg }}<br>
                            @endforeach
                            </td>
                            <td>{{$task->frequency}}</td>
                            <td>
                                <form action="{{route('tasks.destroy', $task->id)}}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{route('tasks.edit', $task->id)}}"
                                       class="btn btn-dark btn-outline-warning mr-2" title="Edit Task">
                                        <i class="fas fa-edit"></i></a>
                                    <a href="{{route('tasks.clone', $task->id)}}"
                                       class="btn btn-dark btn-outline-primary mr-2" title="Clone Task"><i class="fas fa-clone"></i></a>
                                    <button class="btn btn-dark btn-outline-danger" title="Delete Task"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">There are currently no tasks.</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6">
                                <a href="{{route('tasks.create')}}" class="btn btn-dark btn-outline-primary">New Task</a>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
