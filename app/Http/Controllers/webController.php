<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class webController extends Controller
{
    public function home(Request $request){
      return redirect()->route('Alta Peticion'); 
    }

    public function dataApp(Request $request)
    {
      $token = Request::capture()->cookie('jwt_token');
      $usuario = json_decode(Redis::get('Aplicacion:usuarioLogin:'.sha1($token)));

      $temp_data = session('response_data');
      
      $response = [
        'csrfToken'=>csrf_token(),
        'appName'=> config('api.config.nameApp'),
        'endPointCDN'=> config('api.config.endPointCDN'),
        'usuario' => $usuario,
        'version' => BaseController::leerArchivo('version'),
        'menu' => (new MenuController)->generarMenu($request),
        'header' => Http::get(config('api.config.endPointAPIPanel').'/getVistaGenerica/header')->json()["data"],
        'footer' => Http::get(config('api.config.endPointAPIPanel').'/getVistaGenerica/footer')->json()["data"],
        'response_data' => $temp_data, //Devuelve un json con info para la pagina puntual si es necesario
      ];
      session()->forget('response_data');
      return response()->json($response);
    }

    public function logout(Request $request)
    {
      $token = Request::capture()->cookie('jwt_token');
      Session::flush();
      Redis::set('Aplicacion:usuarioLogin:'.sha1($token),null);
      return redirect(config('api.config.endPointPanel'))->withCookie(cookie('jwt_token', null, -1));
    }

}
