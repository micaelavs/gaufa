@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <table id="tabla" class="table table-striped dataTables dataTable no-footer dtr-inline"
                    style="width:100%;">
                </table>
            </div>
            <div class="row">
              <div class="col-md-6">
              </div>
              <div class="col-md-6 text-right">
                  <a class="btn btn-primary" href="$href_nuevo_rol">NUEVO</a>
              </div>
          </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
      $(document).ready(function () {

        var tabla = $('#tabla').DataTable({
          language: {
            url: window.DatosApp.endPointCDN + '/datatables/1.10.12/Spanish_sym.json',
            decimal: ',',
            thousands: '.',
            infoEmpty: 'No hay datos de personas especificos...'
          },
          processing: true,
          serverSide: true,
          searchDelay: 800,
        ajax: {
            url:  window.location.href+'Ajax',
            contentType: "application/json",
            type: 'get'
        },
        info: true,
        bFilter: true,
        responsive:true,
        columnDefs: [
            { targets: 0, width: '5%', responsivePriority: 1 },
            { targets: 1, width: '20%', responsivePriority: 1 },
            { targets: 2, width: '50%', responsivePriority: 1 },
        ],
        order: [[0, 'asc']],
        columns: [
            {
                title: 'ID',
                name: 'id',
                data: 'id',
                className: 'text-left'
            },
            {
                title: 'Nombre',
                name: 'nombre',
                data: 'nombre',
                className: 'text-left'
            },
            {
                title: 'Descripcion',
                name: 'descripcion',
                data: 'descripcion',
                className: 'text-left'
            },
            {
                title: 'Estado',
                name: 'estado',
                data: 'estado',
                className: 'text-left',
                orderable: false,
                render: function (data, type, row) {
                    var $html = '';
                  if(row.estado == 1){
                    $html += '<i class="fa fa-check text-success"></i>';
                  }else{
                    $html += '<i class="fa fa-times text-danger"></i>';
                  }
                  return $html;
                }
            },
            {
                title: 'Acciones',
                data: 'acciones',
                name: 'acciones',
                className: 'text-center',
                orderable: false,
                render: function (data, type, row) {
                    var $html = '';
                    $html += '<div class="btn-group btn-group-sm">';
                  if(row!=null){
                    $html += ' <a href="/rol/modificacion/'+row.id+'"  data-toggle="tooltip" data-placement="top" title="Modificar Rol" target="_self"><i class="fa fa-pencil-square"></i></a>';
                    $html += ' <a href="/rol/baja/'+row.id+'" class="borrar"  data-toggle="tooltip" data-placement="top" title="Borrar Rol" target="_self"><i class="fa fa-trash"></i></a>';      
                  }
                  $html += '</div>';
                  return $html;
                }
            },
        ]
        });

        /** Consulta al servidor los datos y redibuja la tabla
        * @return {Void}
        */
        function update() {
          tabla.draw();
        }
      });
    </script>
@endpush
