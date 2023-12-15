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
                              <label class="control-label"for="nombre">Nombre<span class="text-danger"> *</span></label>
                              <input type="text" class="form-control" id="nombre" name="nombre" maxlength="255" required>
                          </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="control-label"for="descripcion">Descripción<span class="text-danger"> *</span></label>
                          <textarea class="form-control" id="descripcion" name="descripcion" maxlength="400" rows="2" required></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-9 text-right">
                            <button id="btn-submit" type="submit" class="btn btn-primary" data-placement="left" value=""
                               title="Guardar">GUARDAR</button>
                            <a href="$href_volver" class="btn btn-default">CANCELAR</a>
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
          $('#nombre').focus();

          //Recupero de datos
          let data = window.DatosApp.response_data;
          if(data && data.rol){
            $('#nombre').val(data.rol.nombre);
            $('#descripcion').text(data.rol.descripcion);
          }

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
                          window.location.href = '$href_volver';
                        }, 800);
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
