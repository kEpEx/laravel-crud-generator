@extends('CrudGenerator::layouts.master')

@section('content')


<h2 class="page-header">{{model_uc}}</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        Add/Modify {{model_uc}}
    </div>

    <div class="panel-body">
                
        <form action="{{ url('/{{model_plural}}') }}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="task" class="col-sm-3 control-label">{{model_uc}}</label>

                <div class="col-sm-6">
                    <input type="text" name="name" id="task-name" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Add {{model_uc}}
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>






@endsection