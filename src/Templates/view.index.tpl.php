@extends('cataloguemaker::layouts.master')

@section('content')


    <!-- Current Tasks -->
    @if (count(${{model_plural}}) > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                Current Tasks
            </div>

            <div class="panel-body">
                <table class="table table-striped task-table">

                    <!-- Table Headings -->
                    <thead>
                        <th>Task</th>
                        <th>Usuario</th>
                        <th>&nbsp;</th>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach (${{model_plural}} as $d)
                            <tr>
                                <!-- Task Name -->
                                <td class="table-text">
                                    <div>{{ $d->name }}</div>
                                </td>
                                <td class="table-text">
                                    <div>{{ $d->user_name }}</div>
                                </td>

                                <td>
                                    <!-- TODO: Delete Button -->
                                     <form action="{{url('{{model_plural}}/destroy')}}/{{ $d->id }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}

                                        <button>Delete Task</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection