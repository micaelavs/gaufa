@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9"></div>
            <div class="col-md-3">
                <label class="float-md-right control-label">
                    <p><i>Campos requeridos(*)</i></p>
                </label>
            </div>
            <div class="col-md-12">
              <div id="error-text" class="alert alert-danger text-center text-dark" style="display:none;"></div>
              <div id="success-text" class="alert alert-success text-center text-dark" style="display:none;"></div>
            </div>
            <div class="col-md-12">
                <form id="formulario" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"for="nombre">Nombre</label>
                                <label class="form-control" id="nombre" readonly></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">Método</label>
                                <label class="form-control" id="accion" readonly></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label" for="permisos">Roles Autorizados<span class="text-danger">*</span></label>
                                <select class="form-control" id="permisos" name="permisos[]" multiple="multiple" required>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-9 text-right">
                            <button type="submit" class="btn btn-primary" data-placement="left" value=""
                                id='btn-submit' title="Guardar">GUARDAR</button>
                            <a href="$href_listado" class="btn btn-default">CERRAR</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
          $('#permisos').focus();

          // ==== Recupero de datos  ====
          let data = window.DatosApp.response_data;
          if(data && data.enrutado){
            $('#nombre').text(data.enrutado.nombre);
            $('#accion').text(data.enrutado.accion);
          }
          if(data && data.selectRoles){
            $('#permisos').append(data.selectRoles);
          }
          // ===================
          
          $('#formulario').submit(function (e) { 
            e.preventDefault();
            formData = $(this).serialize();

            $.ajax({
                    type: 'POST',
                    url: window.location.href,
                    data: formData,
                    beforeSend: function(response){
                      $("#btn-submit").attr('disabled',true);
                    },
                    success: function(response) {
                        // Maneja la respuesta exitosa
                        $("#success-text").html(response.data).show().delay(2000).fadeOut(500);
                        setTimeout(() => {
                          window.location.href = '$href_listado';
                        }, 2500);
                    },
                    error: function(response) {
                        // Maneja la respuesta fallida
                        let resp = response.responseJSON;
                        let errorHtml = '';
                        for (let key in resp.errors) {
                            for (let i = 0; i < resp.errors[key].length; i++) {
                                errorHtml += '<p>' + resp.errors[key][i] + '</p>';
                            }
                        }
                        $("#error-text").html(errorHtml).show().delay(2000).fadeOut(500);
                        $("#btn-submit").removeAttr('disabled');
                    }
                });

          });
        });
    </script>
@endpush
