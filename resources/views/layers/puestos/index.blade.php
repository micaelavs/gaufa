@extends('app')

@section('content')
    <div class="container" id="listado-modulos">
        <div class="row justify-content-center">
            <div role="main" class="col-md-12">
                <div class="col-sm-12">
                    <table id="tabla" class="table table-striped dataTables dataTable no-footer dtr-inline" style="width:100%;"></table>
                </div>
                <div class="row d-flex align-items-center">
                    <div class="col-md-6">
                        <button class="btn btn-default btn_au accion_exportador ml-3 mr-auto" id="btnDescargarExcel">Descargar Excel</button>
                    </div>
                    <div class="col-md-6 text-right">                    
                        <a class="btn btn-primary" href="{{ route('Alta Puesto') }}">NUEVO</a>                        
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            var tabla = $('#tabla').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 800,
                ajax: {
                    url: window.location.href,
                    contentType: "application/json",
                    type: 'post'
                },
                info: true,
                bFilter: true,
                responsive: true,
                columnDefs: [{
                        targets: 0,
                        width: '5%',
                        responsivePriority: 1
                    },
                    {
                        targets: 1,
                        width: '50%',
                        responsivePriority: 1
                    },
                ],
                order: [
                    [0, 'asc']
                ],
                columns: [
                    {
                        title: 'Nombre',
                        name: 'nombre',
                        data: 'nombre',
                        className: 'text-left'
                    },
                    {
                        title: 'Acciones',
                        data: 'acciones',
                        name: 'acciones',
                        className: 'text-center',
                        orderable: false,
                        render: function(data, type, row) {
                            var $html = '';
                            $html += '<div class="btn-group btn-group-sm">';
                            if (row != null) {
                                $html += ' <a href="/puestos/modificacion/'+row.id+'"  data-toggle="tooltip" data-placement="top" title="Modificar Puesto" target="_self"><i class="fa fa-pencil-square"></i></a>';
                                $html += ' <a href="/puestos/baja/'+row.id+'" class="borrar"  data-toggle="tooltip" data-placement="top" title="Borrar" target="_self"><i class="fa fa-trash"></i></a>';      
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
    <script src="./js/FileSaver.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#btnDescargarExcel').click(function() {
        $.ajax({
            url: '/puestos/listadoExcel',
            type: 'GET',
            xhrFields: {
            responseType: 'blob'
            },
            success: function(response) {
                var blob = new Blob([response], {type: 'application/vnd.ms-excel'});
                saveAs(blob, 'listado_puestos.xlsx');
            }
        });
        });
    });
    </script>


@endpush
