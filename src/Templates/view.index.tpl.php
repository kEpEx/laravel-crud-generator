@extends('CrudGenerator::layouts.master')

@section('content')


<h2 class="page-header">{{ ucfirst('{{model_plural}}') }}</h2>

<div class="panel panel-default">
    <div class="panel-heading">
        List of {{ ucfirst('{{model_plural}}') }}
    </div>

    <div class="panel-body">
        <div class="">
            <table class="table table-striped" id="thegrid">
              <thead>
                <tr>
                  {{htmlcolumns}}
                  <th>Delete</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
        </div>

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
                "ajax": "{{url('{{model_plural}}/grid')}}",
                "columnDefs": [
                    {
                        "render": function ( data, type, row ) {
                            return '<a href="{{url('{{model_plural}}/modify')}}/'+row[0]+'">'+data +'</a>';
                        },
                        "targets": 1
                    },
                    {
                        "render": function ( data, type, row ) {
                            return '<a href="{{url('{{model_plural}}/delete')}}/'+row[0]+'" class="btn btn-danger">Delete</a>';
                        },
                        "targets": {{num_columns}}
                    },
                ]
            });
        });
    </script>
@endsection