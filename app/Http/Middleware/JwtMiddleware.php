<?php




namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class JwtMiddleware

{

  // Definir la lista blanca de tokens

  protected $configApiPanel;

  public function __construct()

  {
    // Obtener la lista blanca de tokens desde la configuración
    $this->configApiPanel = config('api.config');
  }

  public function handle(Request $request, Closure $next)

  {
    $token = Request::capture()->cookie('jwt_token'); 

    // Desencriptar la cookie utilizando la clave de cifrado de Laravel
    if ($token) {

      $usuario = json_decode(Redis::get('Aplicacion:usuarioLogin:'.sha1($token)));
      if($usuario){
        return $next($request);
      }
      
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
      ])->post($this->configApiPanel["endPointAPIPanel"] . '/validarToken/' . $this->configApiPanel["idModuloApp"]);

      if ($response->failed()) {
        Session::flush();
        abort(403, "Token de Autentificación inválido");
      }

      $response = json_decode($response->body());
      if (!$response || !property_exists($response->data, 'id') || !$response->data->permiso->permiso) {
        abort(403, "El usuario no tiene autorizacion");
      } else {
        $ttl = 120;
        Redis::setex('Aplicacion:usuarioLogin:'.sha1($token),$ttl,json_encode($response->data));
      }

      return $next($request);

      if ($response->failed()) {
        Session::flush();
        abort(403, "Token de Autentificación inválido");
      }

    }
    return new RedirectResponse(url($this->configApiPanel['endPointPanel']));
  }
}
