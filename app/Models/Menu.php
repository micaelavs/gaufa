<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;

class Menu extends Model
{

    public $id; // ID numerico definido por el programador
    public $nombre; // Nombre de la ruta, tal cual está defina en ->name() dentro de routes/web.php
    public $url; // Url de la ruta
    public $hijos; //Array de Elementos Menu, tipo hijos
    public $padre;  //Id del elemento Menu que es su padre

    public function __construct(Request $request,$id, $nombre, $hijos = [], $padre = null) {
      $token = Request::capture()->cookie('jwt_token');
      $usuario = json_decode(Redis::get('Aplicacion:usuarioLogin:'.sha1($token))); 
      $query = Enrutado::where('nombre',$nombre);
      if(!$usuario->is_user_super){
        $query->whereJsonContains('permisos',$usuario->permiso->permiso);
      }
      $enrutado = $query->first();
      $this->id = $id;
      $this->nombre = $nombre;
      $this->url = ($enrutado) ? $enrutado->url : null;
      $this->hijos = $hijos;
        
    }
    
    public function agregarHijo(Menu $hijo) {
        $this->hijos[] = $hijo;
    }

    public function setPadre($padre) {
        $this->padre = $padre;
    }

    // Devuelve true o false si la url es la misma en la que está el usuario actualmente
    public function isActive($url){
      $decodedUrl = urldecode($url);
      $route = parse_url($decodedUrl, PHP_URL_PATH);
      return (ltrim($route,'/') == $this->url) ? true : false;
    }

    public function toArray() {
      $menu = [
          'id' => $this->id,
          'nombre' => $this->nombre,
          'url' => $this->url,
          'hijos' => [],
          'padre' => $this->padre,
          'activo' => $this->activo
      ];

      foreach ($this->hijos as $hijo) {
          $menu['hijos'][] = $hijo->toArray();
      }

      return $menu;
  }

}
