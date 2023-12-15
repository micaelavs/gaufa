@extends('app')

@section('content')
    <div class="container">
        <div class="row">
          
              <div class="col-md-2"></div>  
              <div class="col-md-8">
                  <fieldset>
                    <div class="alert alert alert-success text-center">
                      <div class="media">
                        <div class="media-center">
                          <i class="glyphicon glyphicon-ok fa-fw fa-4x"></i>
                        </div>
                        <div class="media-body">
                            <h5 style="font-weight:400"><strong>Solicitud registrada a Nombre de: $email</strong><br>Presione "Aceptar" para continuar.</h5>
                        </div>
                      </div>
                    </div>
                    <div class="media">
                        <div class="media-left">
                            <i style="color:#3c763d;" class="glyphicon glyphicon-envelope fa-4x"></i>
                        </div>
                        <div class="media-body">
                            <p style="margin-top:10px; color:#3c763d; font-weight: bold;">Se envió un email a su cuenta, confirmando la solicitud de Alta de Usuario.<br> Será notificado por email cuando tenga sus credenciales de acceso.</p>
                        </div>
                    </div>
                    <div class="text-right">
                      <a href="$href_aceptar" class="btn btn-primary">ACEPTAR</a>
                    </div>  
                  </fieldset>
              </div>   
        </div>
    </div>
@endsection







