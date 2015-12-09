@extends('crudgenerator::layouts.master')

@section('content')


<h2 class="page-header">[[model_uc]]</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        Add/Modify [[model_uc]]
    </div>

    <div class="panel-body">
                
        <form action="{{ url('/[[model_plural]]/save') }}" method="POST" class="form-horizontal">
            {{ csrf_field() }}

            [[foreach:columns]]
            [[if:i.type=='id']]
            <div class="form-group">
                <label for="[[i.name]]" class="col-sm-3 control-label">[[i.name]]</label>
                <div class="col-sm-6">
                    <input type="text" name="[[i.name]]" id="[[i.name]]" class="form-control" value="{{$model['[[i.name]]'] or ''}}" readonly="readonly">
                </div>
            </div>
            [[endif]]
            [[if:i.type=='text']]
            <div class="form-group">
                <label for="[[i.name]]" class="col-sm-3 control-label">[[i.name]]</label>
                <div class="col-sm-6">
                    <input type="text" name="[[i.name]]" id="[[i.name]]" class="form-control" value="{{$model['[[i.name]]'] or ''}}">
                </div>
            </div>
            [[endif]]
            [[endforeach]]

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i> Save
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>






@endsection