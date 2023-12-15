<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Enrutado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;


class ActualizarEnrutadoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
      // Obtener la fecha de modificación del archivo "routes/web.php"
      $lastModified = File::lastModified(base_path('routes/web.php'));
      $enrutado = Enrutado::select('updated_at')->orderBy('updated_at','desc')->first();
      if(empty($enrutado) || $enrutado->updated_at->timestamp < $lastModified){
        DB::table('enrutado')->update(['estado' => 0]);
        foreach (Route::getRoutes() as $item) {
            $middlewares = $item->gatherMiddleware();
            if (!in_array('ActualizarEnrutadoMiddleware', $middlewares)) {
                continue;
            }
            $ruta = Enrutado::where('nombre','=',$item->getName())->first();
            if($ruta){
              $ruta->url = $item->uri();
              $ruta->accion = $item->getAction()['controller'];
              $ruta->estado = 1;
              $ruta->save();
            }else{
              $ruta = new Enrutado();
              $ruta->url = $item->uri();
              $ruta->nombre = $item->getName();
              $ruta->accion = $item->getAction()['controller'];
              $ruta->estado = 1;
              $ruta->save();
            }
        }
        DB::table('enrutado')->where('estado','=',0)->delete();
      }
      return $next($request);
    }
}
