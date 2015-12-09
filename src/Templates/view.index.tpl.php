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
                        <th>[[i.name]]</th>
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
        var theGrid = null;
        $(document).ready(function(){
            theGrid = $('#thegrid').DataTable({
                "processing": true,
                "serverSide": true,
                "ordering": false,
                "ajax": "{{url('[[model_plural]]/grid')}}",
                "columnDefs": [
                    {
                        "render": function ( data, type, row ) {
                            return '<a href="{{url('[[model_plural]]/show')}}/'+row[0]+'">'+data +'</a>';
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
                            return '<a href="{{url('[[model_plural]]/delete')}}" onclick="return doDelete('+row[0]+')" class="btn btn-danger">Delete</a>';
                        },
                        "targets": [[num_columns]]+1
                    },
                ]
            });
        });
        function doDelete(id) {
            if(confirm('You really want to delete this record?')) {
               $.ajax('{{url('[[model_plural]]/delete')}}/'+id).success(function() {
                theGrid.ajax.reload();
               });
                
            }
            return false;
        }
    </script>
@endsection