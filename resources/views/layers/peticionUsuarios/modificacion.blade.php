@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="page-header header-half">
                <h2 class="text-left">Usuarios<small> | Modificación de solicitud de Usuario</small></h2>
            </div>
            <div class="col-md-9"></div>
            <div class="col-md-3">
                <label class="float-md-right control-label">
                    <p><i>Campos requeridos(*)</i></p>
                </label>
            </div>
            
            <form name="peticionUsuario" id="peticionUsuario" action="" class="">
                 <div class="row">   
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="email">Email<span class="required">*</span></label>
                            <input disabled="disabled" type='email' class="form-control" id="email" name="email" required placeholder="mlopez@fase.gob.ar" value="">
                            <input type="hidden" id="id" name="id">
                        </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="dni">DNI<span class="required">*</span></label>
                            <input disabled="disabled" type='number' class="form-control" id="dni" name="dni" required value="">
                        </div> 
                    </div>  
                </div>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="apellido">Apellido<span class="required">*</span></label>
                            <input disabled="disabled" type='text' class="form-control" id="apellido" name="apellido" required value="">
                        </div> 
                    </div> 
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label" for="nombre">Nombre<span class="required">*</span></label>
                            <input disabled="disabled" type='text' class="form-control" id="nombre" name="nombre" required value="">
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
                <div class="col-md-3"></div>
                <div class="col-md-6 text-right">
                    <button id="btn-submit" type="submit" class="btn btn-primary" data-placement="left" name="boton_alta" value="" title="Guardar">EDITAR</button>
                    <a href="$href_volver" class="btn btn-default" >CANCELAR</a>
                </div>
            </form>
            <div class="modal" id="miModal" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Confirmación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p>Va a modificar la solictud de usuario. ¿Está seguro de realizar esta acción?</p>
                  </div>
                    <div id="error-text" class="text-center text-danger" style="padding-top: 15px"></div>
                    <div id="success-text" class="text-center text-success" style="padding-top: 15px"></div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnConfirmar">Confirmar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCancelar">Cancelar</button>
                  </div>
                </div>
              </div>
            </div> 
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

        $('#puesto').focus();

          //Recupero de datos
          let data = window.DatosApp.response_data;
          if(data && data.peticion){
            $('#email').val(data.peticion.email);
            $('#dni').val(data.peticion.dni);
            $('#apellido').val(data.peticion.apellido);
            $('#nombre').val(data.peticion.nombre);
            $('#puesto').val(data.peticion.id_puesto);
            $('#area').val(data.peticion.id_area);
            $('#id').val(data.peticion.id);
          }
        
            $('#btn-submit').on('click', function(){
                 event.preventDefault();
                $('#miModal').show();

                });

                   $('#btnConfirmar').on('click', function() {
                        formData = $('#peticionUsuario').serialize();
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
                              window.location.href = '$url_listado';
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

                $('#btnCancelar').on('click', function() {
                $('#miModal').hide();
                });   
           
        });
    </script>
@endpush







