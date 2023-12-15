<?php

namespace App\Http\Middleware;

use App\Models\Enrutado;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class RolPermisoMiddleware
{

  protected $jwt_token;

  public function __construct()
  {
    $jwt_token = Request::capture()->cookie('jwt_token');
    $this->jwt_token = $jwt_token;
  }

  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    if ($this->jwt_token) {

      //Obtenemos la ruta a la que se quiere acceder
      $ruta = Enrutado::select('permisos')->where('url', '=', $request->path())
        ->where('nombre', '=', $request->route()->getName())
        ->where('accion', '=', $request->route()->getActionName())->first();

      //Traer por API si el usuario de la cookie is_usep_super
      $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . $this->jwt_token
      ])->post(config('api.config.endPointAPIPanel') . '/validarToken/' . config('api.config.idModuloApp'));

      if ($response->failed()) {
        session(['error_response' => 'Error 403 - Token de Autentificación inválido']);
        return redirect()->route('error');
      }


      $usuario = json_decode($response->body());
      if (property_exists($usuario, 'errors')) {
        session(['error_response' => 'Error 403 - Usuario no encontrado']);
        return redirect()->route('error');
      }

      //Validamos que exista la ruta, que tenga permisos definidos esa ruta y que el usuario no sea super
      if ($ruta && $ruta->permisos && !$usuario->data->is_user_super) {

        //Validamos que el rol del usuario esté incluido dentro de los roles permitidos
        if (in_array($usuario->data->permiso->permiso, $ruta->permisos)) {
          return $next($request);
        } else {
          if ($request->isMethod('GET')) {
            session(['error_response' => 'Error 403 - No tiene permiso para acceder a esta página']);
            return redirect()->route('error');
          } else {
            return response()->json([
              'errors' => ['messages' => ['No tiene permiso para acceder a esta página.']],
              'data' => null
            ], 403);
          }
        }
      }
    }
    return $next($request);
  }
}
