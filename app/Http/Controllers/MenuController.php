<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class MenuController extends Controller
{
  public function generarMenu(Request $request)
  {
    $html = '';
    $token = Request::capture()->cookie('jwt_token'); 
    $usuario = json_decode(Redis::get('Aplicacion:usuarioLogin:'.sha1($token)));
    if ($usuario) {
      $icono = 'fa-home';
      $url = route('home');
      $html .= $this->iconoMenuHtml($icono,$url);

      $titulo = 'Roles';
      $opcion1 = new Menu($request, 1, 'Listado Roles');
      $elementos = [$opcion1];
      $html .= $this->subMenuHtml($titulo, $elementos, $request);

      $titulo = 'Puestos';
      $opcion1 = new Menu($request, 1, 'Listado Puestos');
      $elementos = [$opcion1];
      $html .= $this->subMenuHtml($titulo, $elementos, $request);

      $titulo = 'Peticiones';
      $opcion1 = new Menu($request, 1, 'Alta Peticion');
      $opcion2 = new Menu($request, 1, 'Listado Peticiones');
      $elementos = [$opcion1,$opcion2];
      $html .= $this->subMenuHtml($titulo, $elementos, $request);

      $titulo = 'Enrutado';
      $opcion1 = new Menu($request, 1, 'Listado Rutas');
      $elementos = [$opcion1];
      $html .= $this->subMenuHtml($titulo, $elementos, $request);
      
      $titulo = 'Auditorias';
      $opcion1 = new Menu($request, 1, 'Listado Auditorias');
      $elementos = [$opcion1];
      $html .= $this->subMenuHtml($titulo, $elementos, $request);

      $titulo = 'Usuarios';
      $opcion1 = new Menu($request, 1, 'Listado Usuarios');
      $elementos = [$opcion1];
      $html .= $this->subMenuHtml($titulo, $elementos, $request);
    
      $icono = 'fa-sign-out';
      $url = route('logout');
      $html .= $this->iconoMenuHtml($icono,$url);
    
    }
    return $html;
  }

  private function subMenuHtml($titulo, $elementos, $request)
  {
    $html = '<li class="dropdown">';
    $html .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . $titulo . '<span class="caret"></span></a>';
    $html .= '<ul class="dropdown-menu">';

    $contador = 0;

    foreach ($elementos as $elemento) {
      if (empty($elemento->padre) && !empty($elemento->url)) {
        $html .= '<li class="dropdown-submenu">';
        $active = ($elemento->isActive($request->from)) ? 'active' : '';
        $html .= '<a href="' . $elemento->url . ' " class="'. $active .'" data-toggle="dropdown">' . $elemento->nombre . '</a>';
        if (!empty($elemento->hijos)) {
          $html .= '<ul class="dropdown-menu">';
          foreach ($elemento->hijos as $subelemento) {
            if ($subelemento->padre == $elemento->id && !empty($subelemento->url)) {
              $html .= '<li>';
              $active = ($subelemento->isActive($request->from)) ? 'active' : '';
              $html .= '<a href="' . $subelemento->url . '" class="' . $active . '">' . $subelemento->nombre . '</a>';
              if (!empty($subelemento->hijos)) {
                $html .= '<ul class="dropdown-menu">';
                foreach ($subelemento->hijos as $subsubelemento) {
                  if ($subsubelemento->padre == $subelemento->id && !empty($subsubelemento->url)) {
                    $html .= '<li>';
                    $active = ($subsubelemento->isActive($request->from)) ? 'active' : '';
                    $html .= '<a href="' . $subsubelemento->url . '" class="' . $active . '">' . $subsubelemento->nombre . '</a>';
                    if (!empty($subsubelemento->hijos)) {
                      $html .= '<ul class="dropdown-menu">';
                      foreach ($subsubelemento->hijos as $subsubsubelemento) {
                        if ($subsubsubelemento->padre == $subsubelemento->id && !empty($subsubsubelemento->url)) {
                          $html .= '<li>';
                          $active = ($subsubsubelemento->isActive($request->from)) ? 'active' : '';
                          $html .= '<a href="' . $subsubsubelemento->url . '" class="' . $active . '">' . $subsubsubelemento->nombre . '</a>';
                          $html .= '</li>';
                          $contador++;
                        }
                      }
                      $html .= '</ul>';
                    }
                    $html .= '</li>';
                    $contador++;
                  }
                }
                $html .= '</ul>';
              }
              $html .= '</li>';
              $contador++;
            }
          }
          $html .= '</ul>';
        }
        $html .= '</li>';
        $contador++;
      }
    }

    
    $html .= '</ul>';
    $html .= '</li>';

    $html = ($contador == 0) ? '' : $html;

    return $html;
  }

  private function iconoMenuHtml($icono, $url){
    $html = '<li class="dropdown">';
    $html .= '<a class="opcion" href="'.$url.'"><i class="fa '.$icono.'" aria-hidden="true"></i></a>';           
    $html .= '</li>';

    return $html;
  }

}
