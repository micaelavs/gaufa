<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;

class BaseController extends Controller
{
  //Controlador que sirve para funciones genericas
  public static function leerArchivo($file_path)
  {
    $rutaArchivo = base_path($file_path);

    if (file_exists($rutaArchivo)) {
      $contenidoArchivo = file_get_contents($rutaArchivo);
      return $contenidoArchivo;
    } else {
      return null;
    }
  }


  public static function CryptsOrDeletesAjaxElements($data, $crypts_colums = [], $deletes_columns = [])
  {

    $data = array_map(function ($data) use ($crypts_colums) {
      for ($i = 0; $i < count($crypts_colums); $i++) {
        if (array_key_exists($crypts_colums[$i], $data)) {
          $data[$crypts_colums[$i]] = Crypt::encryptString($data[$crypts_colums[$i]]);
        }
      }
      return $data;
    }, $data);

    $data = array_map(function ($data) use ($deletes_columns) {
      for ($i = 0; $i < count($deletes_columns); $i++) {
        if (array_key_exists($deletes_columns[$i], $data)) {
          unset($data[$deletes_columns[$i]]);
        }
      }
      return $data;
    }, $data);

    return $data;
  }

  public static function agregarInfoJson($json, $modelo, $funcion, $nombreKey)
  {
      // Decodificar el JSON
      $data = $json;
      
      // Verificar si el JSON contiene la clave "data"
      if (isset($data)) {
          // Recorrer los elementos del JSON y aplicar la función
          foreach ($data as &$item) {
              $respuesta =(new $modelo)->$funcion($item);
              $item->{$nombreKey} = $respuesta;
          }
      }
      
      // Volver a codificar el JSON
      return $data;
  }

  /**
   * Funcion que construye listado de opciones en formato HTML para un select (Puede ser multiple o no)
   * @data Puede ser un Array o un Collet de Objetos
   * @colum_value Es la columna que se va a usar como valor del option
   * @colum_option Es la columna que se va a mostrar en el option
   * @colum_filter Es el filtro que se puede aplicar para definir si se agrega o no el option (Puede ser uno solo)
   * @value_filter Es el valor a definir en el filtro
   * @seleccionar puede ser un string, un numero o un arreglo de valores a seleccionar.
   */
  public static function htmlSelect($data = [], $colum_value = null, $colum_option = null, $column_filter = null, $value_filter = null, $seleccionar = null)
  {
    $html = '';
    foreach ($data as $item) {
      $selected = null;
      if (is_object($item)) {
        if ($item->{$column_filter} == $value_filter || empty($item->{$column_filter})) {
          if ($seleccionar && is_array($seleccionar) && in_array($item->{$colum_value}, $seleccionar)) {
            $selected = 'selected';
          } elseif ($seleccionar && $item->{$colum_value} == $seleccionar) {
            $selected = 'selected';
          }
          $html .= '<option value="' . $item->{$colum_value} . '" ' . $selected . '>' . $item->{$colum_option} . '</option>';
        }
      } else {
        if ($item[$column_filter] == $value_filter || empty($item[$column_filter])) {
          if ($seleccionar && is_array($seleccionar) && in_array($item[$colum_value], $seleccionar)) {
            $selected = 'selected';
          } elseif ($seleccionar && is_string($seleccionar) && $item[$colum_value] == $seleccionar) {
            $selected = 'selected';
          }
          $html .= '<option value="' . $item[$colum_value] . '">' . $item[$colum_option] . '</option>';
        }
      }
    }
    return $html;
  }

}
