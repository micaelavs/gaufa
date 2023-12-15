<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <base href="/" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="">
    <title></title>
    <link rel="shortcut icon" href="https://www.transporte.gob.ar/_img/favicon.png" />
    <link rel="stylesheet" href='/css/principal.css' />

   <!-- Scripts -->
   <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
   <script src="/js/app.js" defer></script>
   <script src="{{env('URL_CDN')}}/momentjs/2.14.1/moment.min.js"></script>
   <script src="{{env('URL_CDN')}}/momentjs/2.14.1/es.js"></script>
   <script src="{{env('URL_CDN')}}/bootstrap/datepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
   <script src="{{env('URL_CDN')}}/datatables/1.10.12/datatables.min.js"></script>
   <script src="{{env('URL_CDN')}}/datatables/defaults.js"></script>
   <script src="js/Spanish_sym.js"></script>

   <!-- Fuentes -->
   <link href="{{env('URL_CDN')}}/poncho-v01/css/roboto-fontface.css" type='text/css' rel="stylesheet"/>
   <link href="{{env('URL_CDN')}}/poncho-v01/css/font-awesome.min.css" type="text/css" rel="stylesheet"/>
   <!-- Estilos -->
   <link href="{{env('URL_CDN')}}/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
   <link href="{{env('URL_CDN')}}/bootstrap/datepicker/4.17.37/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
   <link href="{{env('URL_CDN')}}/poncho-v01/css/droid-serif.css" rel="stylesheet" />
   <link href="{{env('URL_CDN')}}/poncho-v01/css/poncho.min.css" rel="stylesheet" />
   <link href="{{env('URL_CDN')}}/estiloIS/5/estilois.css" rel="stylesheet" />

    @stack('scripts')
</head>

<body>
    <div id="loading-overlay">
        <div id="loading-spinner"></div>
    </div>
    <div id="app">
        <div id="header"></div>

        <main class="py-4">
            @yield('content')
        </main>
        
        <div id="footer">

        </div>
        
    </div>
</body>

</html>
