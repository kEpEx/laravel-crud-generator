@extends('crudgenerator::layouts.master')

@section('content')


<h2 class="page-header">{{ ucfirst('[[model_plural]]') }}</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        List of {{ ucfirst('[[model_plural]]') }}
    </div>

    <div class="panel-body">
        <div class="">
            <table class="table table-striped" id="thegrid">
              <thead>
                <tr>
                    [[foreach:columns]]
                        <th>[[i]]</th>
                    [[endforeach]]
                    <th style="width:50px"></th>
                    <th style="width:50px"></th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
        </div>
        <a href="{{url('[[model_plural]]/add')}}" class="btn btn-primary" role="button">Add [[model_singular]]</a>
    </div>
</div>




@endsection



@section('scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $('#thegrid').DataTable({
                "processing": true,
                "serverSide": true,
                "ordering": false,
                "ajax": "{{url('[[model_plural]]/grid')}}",
                "columnDefs": [
                    {
                        "render": function ( data, type, row ) {
                            return '<a href="{{url('[[model_plural]]/modify')}}/'+row[0]+'">'+data +'</a>';
                        },
                        "targets": 1
                    },
                    {
                        "render": function ( data, type, row ) {
                            return '<a href="{{url('[[model_plural]]/update')}}/'+row[0]+'" class="btn btn-default">Update</a>';
                        },
                        "targets": [[num_columns]]
                    },
                    {
                        "render": function ( data, type, row ) {
                            return '<a href="{{url('[[model_plural]]/delete')}}/'+row[0]+'" class="btn btn-danger">Delete</a>';
                        },
                        "targets": [[num_columns]]+1
                    },
                ]
            });
        });
    </script>
@endsection