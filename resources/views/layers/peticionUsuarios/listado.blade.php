@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
          <div class="row ">
             <label role="button" data-toggle="collapse" href="" aria-expanded="false" aria-controls="collapseFiltros" style="display: block;">
              <span class="">
                <i class="fa fa-filter"></i> Filtros
            </span>
            </label>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label" for="estao">Estado<span class="text-danger"> *</span></label>
                <select class="form-control" id="estado" name="estado">
                    <option value=""> Seleccione </option>
                      $_estados
                </select>
                </div>
            </div>
            <div class="row">
              <div class="alert col-xs-12">
                  <label>Solicitudes:</label>
                  <span class="label label-primary">Pendiente de aprobación</span>&nbsp;
                  <span class="label label-success">Aprobada</span>&nbsp;
                  <span class="label label-danger">Rechazada</span>&nbsp;
              </div>
            </div>
            <div class="col-sm-12">
                <table id="tabla" class="table table-striped dataTables dataTable no-footer dtr-inline"
                    style="width:100%;">
                </table>
            </div>
            <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6 text-right">
                <a class="btn btn-primary" href="$href_nueva_peticion">NUEVO</a>
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
            url: window.location.href + 'Ajax',
            contentType: "application/json",
            type: 'get',
            data: function(d) {
                      filtros_dataTable = $.extend({}, d, {
                          filters : {
                            estado: $('#estado').val(),
                          }
                      });
                      return filtros_dataTable;
                  }
        },
        info: true,
        bFilter: true,
        responsive:true,
        columnDefs: [
            { targets: 0, width: '10%'},
            { targets: 1, width: '10%'},
            { targets: 2, width: '10%'},
            { targets: 3, width: '10%'},
            { targets: 4, width: '10%'},
            { targets: 5, width: '10%'},
            { targets: 6, width: '10%'},
            { targets: 7, width: '10%'},
            { targets: 8, width: '10%'},
            { targets: 9, width: '10%'},
        ],
        order: [[8, 'desc']],
        columns: [
            {
                title: 'ID',
                name: 'id',
                data: 'id',
                className: 'text-left'
            },
            {
                title: 'Fecha',
                name: 'updated_at',
                data: 'updated_at',
                className: 'text-left'
            },
            {
                title: 'Email',
                name: 'email',
                data: 'email',
                className: 'text-left'
            },
            {
                title: 'DNI',
                name: 'dni',
                data: 'dni',
                className: 'text-left'
            },
            {
                title: 'Apellido',
                name: 'apellido',
                data: 'apellido',
                className: 'text-left'
            },
            {
                title: 'Nombre',
                name: 'nombre',
                data: 'nombre',
                className: 'text-left'
            },
            {
                title: 'Puesto',
                name: 'puesto',
                data: 'puesto',
                className: 'text-left'
            },
            {
                title: 'Area',
                name: 'id_area',
                data: 'id_area',
                className: 'text-left'
            },
            {
                title: 'Estado',
                name: 'id_estado',
                data: 'id_estado',
                className: 'text-left',
                orderable: false,
                 render: function (data, type, row) {
                    var $html = '';
                  if(row.id_id_estado == 3){
                     $html += '<span class="label label-default">Cancelada</span>';
                  }else if(row.id_estado == 4){
                    $html += '<span class="label label-primary">Pendiente de aprobación</span>';
                  }else if(row.id_estado == 5){
                     $html += '<span class="label label-success">Aprobada</span>';
                  }else if(row.id_estado == 6){
                    $html += '<span class="label label-danger">Rechazada</span>';
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
                    if(row.id_estado == 4){
                      $html += ' <a href="/peticionUsuario/aprobar/'+row.id+'"  data-toggle="tooltip" data-placement="top" title="Aprobar solicitud" target="_self"><i class="fa fa-check"></i></a>';
                      $html += ' <a href="/peticionUsuario/rechazar/'+row.id+'" class="rechazar"  data-toggle="tooltip" data-placement="top" title="Rechazar solicitud" target="_self"><i class="fa fa-times"></i></a>'; 
                      $html += ' <a href="/peticionUsuario/modificacion/'+row.id+'"  data-toggle="tooltip" data-placement="top" title="Modificar Petición" target="_self"><i class="fa fa-pencil-square"></i></a>';     
                    }
                    
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

        $('#filtrar_btn').click(function() {
              update();
          });

         $('#estado').on('change', update);
      });
    </script>
@endpush
