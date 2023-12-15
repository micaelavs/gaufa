@extends('app')

@section('content')
    <div class="container" id="listado-modulos">
        <div class="row justify-content-center">
            <div role="main" class="col-md-12">
                <div class="page-header">
                    <h1 id="usuario-fullname"></h1>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
  <script>
      $(document).ready(function() {
        let usuarioString = sessionStorage.getItem('usuario');
        let usuario = JSON.parse(usuarioString);
        let appName = sessionStorage.getItem('appName');
        $("#usuario-fullname").html(`${usuario.nombre} ${usuario.apellido} <small> | ${appName}</small>`);
       
      });
  </script>
@endpush
