@extends('[[custom_master]]')

@section('content')



<h2 class="page-header">[[model_uc]]</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        View [[model_uc]]
    </div>

    <div class="panel-body">
                
        <form action="{{ url('/[[route_path]]') }}" method="POST" class="form-horizontal">
        [[foreach:columns]]
        
        <div class="form-group">
            <label for="[[i.name]]" class="col-sm-3 control-label">[[i.display]]</label>
            <div class="col-sm-6">
                <input type="text" name="[[i.name]]" id="[[i.name]]" class="form-control" value="{{$model['[[i.name]]'] or ''}}" readonly="readonly">
            </div>
        </div>
        
        [[endforeach]]

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-6">
                <a class="btn btn-default" href="{{ url('/[[route_path]]') }}"><i class="glyphicon glyphicon-chevron-left"></i> Back</a>
            </div>
        </div>

        </form>
    

    </div>
</div>







@endsection