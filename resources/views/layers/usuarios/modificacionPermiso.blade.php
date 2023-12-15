@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9"></div>
            <label class="float-md-right control-label">
                <p><i>Campos requeridos(*)</i></p>
            </label>
            <div class="col-md-3"></div>
            <div class="col-md-12">
                <div id="error-text" class="alert alert-danger text-center text-dark" style="display:none;"></div>
                <div id="success-text" class="alert alert-success text-center text-dark" style="display:none;"></div>
            </div>
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form id="formulario" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label"for="nombre">Nombre y Apellido</label>
                                <label class="form-control" id="nombre_apellido" readonly></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Username</label>
                                <label class="form-control" id="username" readonly></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label">Documento</label>
                              <label class="form-control" id="documento" readonly></label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label">Email</label>
                              <label class="form-control" id="email" readonly></label>
                          </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="permiso">Permiso<span class="text-danger">*</span></label>
                                <select class="form-control" id="permiso" name="permiso" required>
                                  <option value="" readonly>Seleccione...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-9 text-right">
                          <button type="submit" class="btn btn-success" name="confirmar" value="1" id="btn-submit">CONFIRMAR</button>
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
            $('#permiso').focus();

            //Recupero de datos
            let data = window.DatosApp.response_data;
        
            if (data && data.usuario) {
                $('#nombre_apellido').text(data.usuario.nombre + ' ' + data.usuario.apellido);
                $('#username').text(data.usuario.username);
                $('#documento').text(data.usuario.documento);
                $('#email').text(data.usuario.email);
            }
            if (data && data.selectRoles) {
                $('#permiso').append(data.selectRoles);
            }

            $('#formulario').submit(function(e) {
                e.preventDefault();
                formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: window.location.href,
                    data: formData,
                    beforeSend: function(response) {
                        $("#btn-submit").attr('disabled', true);
                    },
                    success: function(response) {
                        // Maneja la respuesta exitosa
                        $("#success-text").html(response.data).show().delay(1000).fadeOut(500);
                        setTimeout(() => {
                            window.location.href = '$href_listado';
                        }, 1500);
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
