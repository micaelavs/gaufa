@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <table id="tabla" class="table table-striped dataTables dataTable no-footer dtr-inline"
                    style="width:100%;">
                </table>
            </div>
        </div>
        <div class="row">
          <div class="col-md-3"></div>
          <div class="col-md-9 text-right">
              <a href="$href_btn_cerrar" class="btn btn-default">CERRAR</a>
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
          order: [[0, 'asc']],
          responsive:true,
          columns: [
              {
                  title: 'Nombre',
                  name: 'nombre',
                  data: 'nombre',
                  className: 'text-left'
              },
              {
                  title: 'Metodo',
                  name: 'accion',
                  data: 'accion',
                  className: 'text-left'
              },
              {
                  title: 'Roles Permitidos',
                  name: 'roles_permisos',
                  data: 'roles_permisos',
                  className: 'text-left',
                  orderable: false,
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
                      $html += ' <a href="/enrutado/modificacion/'+row.id+'"  data-toggle="tooltip" data-placement="top" title="Modificar contrato" target="_self"><i class="fa fa-pencil-square"></i></a>';
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
