@extends('app')

@section('content')
    <div class="container">
        <div class="row ">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_desde_filtro" class="control-label">Fecha Desde</label>
                    <div class="input-group date fecha_desde_filtro">
                        <input type="text" class="form-control" id="fecha_desde_filtro" name="fecha_desde_filtro"
                            value="">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_hasta_filtro" class="control-label">Fecha Hasta</label>
                    <div class="input-group date fecha_hasta_filtro">
                        <input type="text" class="form-control fecha_hasta_filtro" id="fecha_hasta_filtro"
                            name="fecha_hasta_filtro" value="">
                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                  <button class="btn btn-primary btn-md" style="margin-top:29px;" id="filtrar_btn">FILTRAR</button>
            </div>
        </div>
        <div class="row">
          <div class="col-xs-12 alert alert-warning default-filtro">
            <b>Filtro por defecto de los últimos 7 días.</b>
          </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm-12">
                <table id="tabla"
                    class="table table-striped dataTables dataTable no-footer dtr-inline responsive collapsed"
                    style="width:100%;" role="grid" aria-describedby="tabla_info">
                </table>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            // Asigna un controlador de eventos al botón 'Mostrar/Ocultar Cambios'
            $('body').on('click', '.toggle-changes', function() {
                // Encuentra el contenedor de cambios en la misma fila
                var changesContainer = $(this).siblings('.changes-container');
                var otherChangesContainer = $(this).closest('tr').find('.toggle-changes').not(this)
                    .siblings('.changes-container');

                // Muestra u oculta ambos contenedores de cambios al mismo tiempo
                changesContainer.toggle();
                otherChangesContainer.toggle();

                // Comprueba si los contenedores de cambios están visibles o no
                if (changesContainer.is(':visible') && otherChangesContainer.is(':visible')) {
                    // Cambia el texto de ambos botones a "Ocultar Datos"
                    $(this).html(
                        '<btn btn-primary rounded-circle btn-sm mb-2 toggle-changes"><i class="fa fa-arrow-up"></i></button>'
                    );
                    $(this).closest('tr').find('.toggle-changes').not(this).html(
                        '<btn btn-primary rounded-circle btn-sm mb-2 toggle-changes"><i class="fa fa-arrow-up"></i></button>'
                    );
                } else {
                    // Cambia el texto de ambos botones a "Mostrar Datos"
                    $(this).html(
                        '<btn btn-primary rounded-circle btn-sm mb-2 toggle-changes"><i class="fa fa-arrow-down"></i></button>'
                    );
                    $(this).closest('tr').find('.toggle-changes').not(this).html(
                        '<btn btn-primary rounded-circle btn-sm mb-2 toggle-changes"><i class="fa fa-arrow-down"></i></button>'
                    );
                }
            });

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
                              fecha_desde: $('#fecha_desde_filtro').val(),
                              fecha_hasta: $('#fecha_hasta_filtro').val()
                            }
                        });
                        return filtros_dataTable;
                    }
                },
                info: true,
                bFilter: true,
                responsive: true,
                columnDefs: [{
                        targets: 0,
                        width: '20%',
                        responsivePriority: 1
                    },
                    {
                        targets: 1,
                        width: '20%',
                        responsivePriority: 1
                    },
                    {
                        targets: 2,
                        width: '20%',
                        responsivePriority: 1
                    },
                    {
                        targets: 3,
                        width: '20%',
                        responsivePriority: 1
                    },
                    {
                        targets: 4,
                        width: '20%',
                        responsivePriority: 1
                    },
                ],
                order: [
                    [6, 'desc']
                ],
                columns: [{
                        title: 'ID Registro',
                        name: 'record_id',
                        data: 'record_id',
                        className: 'text-left'
                    },
                    {
                        title: 'Modelo',
                        name: 'model',
                        data: 'model',
                        className: 'text-left'
                    },
                    {
                        title: 'Accion',
                        name: 'action',
                        data: 'action',
                        className: 'text-left'
                    },
                    {
                        title: 'Usuario',
                        name: 'user',
                        data: 'user',
                        className: 'text-left',
                        orderable: false,
                        render: function(data, type, row) {
                            var $html = '';
                            return row.user.username;
                        }
                    },
                    {
                        title: 'Fecha',
                        name: 'created_at',
                        data: 'created_at',
                        className: 'text-left'
                    },
                    {
                        title: 'Antes',
                        name: 'old_data',
                        data: 'old_data',
                        className: 'text-left',
                        orderable: false,
                        render: function(data, type, row) {
                            var $html =
                                '<button class="btn btn-primary rounded-circle btn-sm mb-2 toggle-changes"><i class="fa fa-arrow-down"></i></button>';
                            $html += '<div class="changes-container" style="display:none;">';
                            if (row.old_data) {
                                for (var key in row.old_data) {
                                    if (row.new_data) {
                                        if (row.old_data[key] !== row.new_data[key]) {
                                            $html += '<div class="mb-1"><label>' + key +
                                                ':</label><input type="text" class="form-control" value="' +
                                                row.old_data[key] +
                                                '" readonly style="background-color: orange;"></div>';
                                        } else {
                                            $html += '<div class="mb-1"><label>' + key +
                                                ':</label><input type="text" class="form-control" value="' +
                                                row.old_data[key] + '" readonly></div>';
                                        }

                                    } else {
                                        $html += '<div class="mb-1"><label>' + key +
                                            ':</label><input type="text" class="form-control" value="' +
                                            row.old_data[key] + '" readonly></div>';
                                    }
                                }
                            }
                            $html += '</div>';
                            return $html;
                        }
                    },
                    {
                        title: 'Después',
                        name: 'new_data',
                        data: 'new_data',
                        className: 'text-left',
                        orderable: false,
                        render: function(data, type, row) {
                            var $html =
                                '<button class="btn btn-primary rounded-circle btn-sm mb-2 toggle-changes"><i class="fa fa-arrow-down"></i></button>';
                            $html += '<div class="changes-container" style="display:none;">';
                            if (row.old_data && row.new_data) {
                                for (var key in row.new_data) {
                                    if (row.old_data) {
                                        if (row.old_data[key] !== row.new_data[key]) {
                                            $html += '<div class="mb-1"><label>' + key +
                                                ':</label><input type="text" class="form-control" value="' +
                                                row.new_data[key] +
                                                '" readonly style="background-color: orange;"></div>';
                                        } else {
                                            $html += '<div class="mb-1"><label>' + key +
                                                ':</label><input type="text" class="form-control" value="' +
                                                row.new_data[key] + '" readonly></div>';
                                        }
                                    } else {
                                        $html += '<div class="mb-1"><label>' + key +
                                            ':</label><input type="text" class="form-control" value="' +
                                            row.new_data[key] + '" readonly></div>';
                                    }
                                }
                            }
                            $html += '</div>';
                            return $html;
                        }
                    }

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

            $("#fecha_desde_filtro").datetimepicker({
                format: 'DD/MM/YYYY',
                defaultDate: moment().subtract(7, 'days')
            });

            $("#fecha_hasta_filtro").datetimepicker({
                format: 'DD/MM/YYYY',
                defaultDate: moment()
            });

            $("#fecha_desde_filtro").on("change.datetimepicker", function(e) {
                $('#fecha_hasta_filtro').data("DateTimePicker").minDate(e.date);
            });

            $("#fecha_hasta_filtro").on("change.datetimepicker", function(e) {
                $('#fecha_desde_filtro').data("DateTimePicker").maxDate(e.date);
            });
        });
    </script>
@endpush
