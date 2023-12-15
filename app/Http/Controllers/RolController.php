<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class RolController extends Controller
{

  public function listado(Request $request)
  {
      $view = view('layers.roles.listado')->render();
      $view_modificado = str_replace('$href_nuevo_rol', route('Agregar Rol'), $view);
      return $view_modificado;
  }

  public function listadoAjax(Request $request){
     //Listado por ajax
     $dataTable_columns  = $request->columns;
     $orders  = [];

     foreach ($orden = (array)$request->order as $i => $val) {
       $orders[]  = [
         'column'  => (!empty($tmp = $orden[$i]) && !empty($dataTable_columns) && is_array($dataTable_columns[0]))
           ? $dataTable_columns[(int)$tmp['column']]['data']  : 'id',
         'dir'  => !empty($tmp = $orden[$i]['dir'])
           ? $tmp  :  'desc',
       ];
     }
     $date  = [];
     if ($request->search && array_key_exists('value', $request->search) && preg_match('/^\d{2}\/\d{2}\/\d{4}/', $request->search['value'], $date)) {
       $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $request->search['value']);
       $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
     } else {
       $search = ($request->search && array_key_exists('value', $request->search))? $request->search['value'] : null;
     }
     
     $params  = [
       'order'    => $orders,
       'start'    => !empty($tmp = $request->start)
          ? $tmp : 0,
       'length'  => !empty($tmp = $request->length)
          ? $tmp : 10,
       'search'  => !empty($search)
          ? $search : '',
       'filters'   => (array_key_exists('filters', $request->all())) 
          ? $request->filters : null,
     ];
     $data =  Rol::listarRoles($params);
     return response()->json($data);
  }

  public function alta(Request $request)
  {
    if ($request->isMethod('post')) {
      $rol = Rol::where('nombre', $request->nombre)->first();
      if ($rol && $rol->estado == false) {
        $rol->descripcion = $request->descripcion;
        $rol->estado = true;
        $resp = $rol->save();
      } else {

        $validator = Validator::make($request->all(), [
          'nombre' => 'required|string|max:255|unique:roles',
          'descripcion' => 'string|max:50',
        ]);

        if ($validator->fails()) {
          return response()->json([
            'errors' => $validator->errors(),
            'data' => null,
          ], 422);
        }

        $rol = new Rol();
        $rol->nombre = $request->nombre;
        $rol->descripcion = $request->descripcion;
        $resp = $rol->save();
      }

      return response()->json([
        'errors' => !($resp) ? ['messages' => ['Error al guardar el Rol']] : null,
        'data' => ($resp) ? 'Rol guardado correctamente' : null,
      ], ($resp) ? 200 : 400);
    } else {

      $view = view('layers.roles.alta')->render();
      $view_modificado = str_replace('$href_volver', route('Listado Roles'), $view);
      return $view_modificado;
    }
  }


  public function modificacion(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $validator = Validator::make($request->all(), [
        'nombre' => 'required|string|max:255|unique:roles,nombre,' . $id,
        'descripcion' => 'string|max:50',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'errors' => $validator->errors(),
          'data' => null,
        ], 422);
      }

      $rol = Rol::find($id);
      if (empty($rol)) {
        return response()->json([
          'errors' => ['messages' => ['El rol a modificar no existe.']],
          'data' => null,
        ], 422);
      }

      $rol->nombre = $request->nombre;
      $rol->descripcion = $request->descripcion;
      $resp = $rol->save();

      return response()->json([
        'errors' => !($resp) ? ['messages' => ['Error al guardar el Rol']] : null,
        'data' => ($resp) ? 'Rol guardado correctamente' : null,
      ], ($resp) ? 200 : 400);
    } else {

      $rol = Rol::find($id);
      session(['response_data' => ['rol' => $rol]]);
      $view = view('layers.roles.modificacion')->render();
      $view_modificado = str_replace('$href_volver', route('Listado Roles'), $view);
      return $view_modificado;
    }
  }

  public function baja(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $rol = Rol::find($id);
      if (empty($rol)) {
        return response()->json([
          'errors' => ['messages' => ['El rol a dar de baja no existe.']],
          'data' => null,
        ], 422);
      }

      $rol->estado = 0;
      $resp = $rol->save();

      return response()->json([
        'errors' => !($resp) ?['messages' => [ 'Error al borrar el Rol']] : null,
        'data' => ($resp) ? 'Rol dado de baja correctamente' : null,
      ], ($resp) ? 200 : 400);
    } else {
      $rol = Rol::find($id);
      if (empty($rol)) {
        return route('Listado Roles');
      }
      $view = view('layers.roles.baja')->render();
      $view_modificado = str_replace(['$href_volver', '$texto_baja'], [route('Listado Roles'), "Usted está por borrar el Rol <b>{$rol->nombre}</b>"], $view);
      return $view_modificado;
    }
  }
}
