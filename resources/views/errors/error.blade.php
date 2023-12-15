@extends('app')

@section('content')
    <div class="error-container">
        <h1>$_error</h1>
        <p>Redireccionando en 3 segundos...</p>
    </div>
@endsection
@push('scripts')
    <style>
        body {
            background-color: #fff;
        }

        .error-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            background-image: linear-gradient(to bottom, #89CFF0, #fff);
        }

        h1 {
            color: #1A1A1A;
            font-size: 4rem;
        }

        p {
            color: #1A1A1A;
            font-size: 1.5rem;
        }
    </style>
    <script>
        setTimeout(function() {
            window.location.href =
                '$_redirec_home'; // Reemplaza '$_redirec_home' con la ruta a la que deseas redirigir
        }, 3000);
    </script>
@endpush
