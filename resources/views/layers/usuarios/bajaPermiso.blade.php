@extends('app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-12">
            <div id="error-text" class="alert alert-danger text-center text-dark" style="display:none;"></div>
            <div id="success-text" class="alert alert-success text-center text-dark" style="display:none;"></div>
            <form id="formulario" action="" name="formualrio_borrar" method="POST">
              <fieldset style="text-align: center">
                <div class="alert alert-danger text-center">
                  <div class="media">
                    <div class="media-center">
                      <i class="glyphicon glyphicon-warning-sign fa-fw fa-4x"></i>
                    </div>
                    <div class="media-body child">
                        <h5 style="font-weight:400">$texto_baja</h5>
                        <h5 style="font-weight:400"></h5>
                    </div>
                    <div class="col-md-12">
                      <h5 style="font-weight:400">Sí esta seguro elija <strong>"CONFIRMAR"</strong></h5>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <button type="submit" class="btn btn-success" id="btn-submit">CONFIRMAR</button>
                  <a href="$href_listado" class="btn btn-primary">CANCELAR</a>
                </div>  
              </fieldset>
            </form>  
          </div> 
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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
