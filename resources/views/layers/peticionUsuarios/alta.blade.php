@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="page-header header-half">
                <h2 class="text-left">Usuarios<small> | Solicitud de Alta de Usuario</small></h2>
            </div>
            <div class="col-md-9"></div>
            <div class="col-md-3">
                <label class="float-md-right control-label">
                    <p><i>Campos requeridos(*)</i></p>
                </label>
            </div>
            <div id="error-text" class="text-center text-danger" style="padding-top: 15px"></div>
            <div id="success-text" class="text-center text-success" style="padding-top: 15px"></div>
            <form name="peticionUsuario" id="peticionUsuario" action="" class="">
                 <div class="row">   
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="email">Email<span class="required">*</span></label>
                            <input type='email' class="form-control" id="email" name="email" required placeholder="mlopez@fase.gob.ar" value="">
                        </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="dni">DNI<span class="required">*</span></label>
                            <input type='number' class="form-control" id="dni" name="dni" required value="">
                        </div> 
                    </div>  
                </div>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="apellido">Apellido<span class="required">*</span></label>
                            <input type='text' class="form-control" id="apellido" name="apellido" required value="">
                        </div> 
                    </div> 
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="nombre">Nombre<span class="required">*</span></label>
                            <input type='text' class="form-control" id="nombre" name="nombre" required value="">
                        </div> 
                    </div>    
                </div>       
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <label class="control-label" for="puesto">Puesto<span class="text-danger"> *</span></label>
                        <select class="form-control" id="puesto" name="puesto">
                            <option value=""> Seleccione </option>
                                 $_puestos
                        </select>
                    </div>    
                    <div class="col-md-3">
                        <label class="control-label" for="area">Área<span class="text-danger"> *</span></label>
                        <select class="form-control" id="area" name="area">
                                <option value=""> Seleccione </option>
                                <option value="1">Sistemas</option>
                                <option value="2">RRHH</option>
                        </select>
                    </div> 
                </div>
                <br>
                <div class="row">   
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="password">Password<span class="required">*</span></label>
                            <input type='password' class="form-control" id="password" name="password" required value="">
                        </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="validar_password">Confirmación Password<span class="required">*</span></label>
                            <input type='password' class="form-control" id="confirmacion_password" name="confirmacion_password" required value="">
                        </div> 
                    </div>  
                </div>    
                 <div class="col-md-3"></div>
                <div class="col-md-6 text-right">
                    <button id="btn-submit" type="submit" class="btn btn-primary" data-placement="left" name="boton_alta" value="" title="Guardar">SOLICITAR ACCESO</button>
                    <a href="$href_volver" class="btn btn-default" >CANCELAR</a>
                </div>
            </form>
                
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

          $('#email').focus();
          $('#peticionUsuario').submit(function (e) { 
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
                        $("#error-text").html('').hide();
                        $("#success-text").html(response.data).show().delay(2000).fadeOut(500);
                        setTimeout(() => {
                          window.location.href = '$url_confirmacion';
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
                        $("#error-text").html(errorHtml).show();
                        $("#btn-submit").removeAttr('disabled');
                    }
                });

          });
        });
    </script>
@endpush







