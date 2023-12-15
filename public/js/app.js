window.DatosApp = null;
let currentUrl = window.location.href;
$.ajax({
  url: '/dataApp',
  data: {from: encodeURIComponent(currentUrl)},
  async: false,
  success: function(response) {
    window.DatosApp = response;
    $('meta[name="csrf-token"]').attr('content', window.DatosApp.csrfToken);
    $('title').text(window.DatosApp.appName);
    $('#header').html(window.DatosApp.header);
    $('#footer').html(window.DatosApp.footer);
    $('#version-footer').text(window.DatosApp.version);

    $.ajaxSetup({ headers: {'X-CSRF-TOKEN': window.DatosApp.csrfToken} });
    if(window.DatosApp.usuario){
      $('#nav-elements').prepend(window.DatosApp.menu);
    }
  }
});

$(window).on('load', function() {
  $('#loading-overlay').fadeOut();
  let token = $('meta[name="csrf-token"]').attr('content');
  $('form[method="post"], form[method="POST"]').each(function() { // Seleccionar todos los formularios con el atributo method="post"
    $(this).append('<input type="hidden" name="_token" value="' + token + '">'); // Agregar un campo oculto con el valor del token CSRF a cada formulario
  });
});