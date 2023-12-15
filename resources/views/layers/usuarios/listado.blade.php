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
            type: 'get',
           
        },
        error: function(xhr, error, thrown) {
          alert(xhr.responseText);
        },
        info: true,
        bFilter: true,
        responsive:true,
        columnDefs: [
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
                title: 'Username',
                name: 'username',
                data: 'username',
                className: 'text-left'
            },
            {
                title: 'Nombre',
                name: 'nombre',
                data: 'nombre',
                className: 'text-left'
            },
            {
                title: 'Apellido',
                name: 'apellido',
                data: 'apellido',
                className: 'text-left'
            },
            {
                title: 'Documento',
                name: 'documento',
                data: 'documento',
                className: 'text-left'
            },
            {
                title: 'Rol',
                name: 'rol',
                data: 'rol',
                className: 'text-left'
            },
            {
                title: 'Email',
                name: 'email',
                data: 'email',
                className: 'text-left',
                render: function (data, type, row) {
                  var $html = '<a href="mailto:'+row.email+'"">'+row.email+'</a>';
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
                    $html += ' <a href="/usuarios/modificacionPermiso/'+row.id+'"  data-toggle="tooltip" data-placement="top" title="Modificar Rol en Sistema" target="_self"><i class="fa fa-user"></i></a>';
                    $html += ' <a href="/usuarios/bajaPermiso/'+row.id+'" class="borrar"  data-toggle="tooltip" data-placement="top" title="Qutar Usuario del Sistema" target="_self"><i class="fa fa-trash"></i></a>';      
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
