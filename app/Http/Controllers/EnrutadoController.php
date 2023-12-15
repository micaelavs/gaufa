<?php

namespace App\Http\Controllers;

use App\Models\Enrutado;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class EnrutadoController extends Controller
{

  public function listado(Request $request)
  {
      $view = view('layers.enrutado.listado')->render();
      $view_modificado = str_replace('$href_btn_cerrar', route('Alta Peticion'), $view);
      return $view_modificado;
  }

  public function listadoAjax(Request $request){
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
      $data =  Enrutado::listarRutas($params);
      return Response::json($data, 200);
  }

  public function modificacion(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      try {
        $enrutado = Enrutado::find($id);
        $validator = Validator::make($request->all(), [
          'permisos' => 'nullable|required'
        ]);

        if ($validator->fails()) {
          return response()->json([
            'errors' => $validator->errors(),
            'data' => null,
          ], 400);
        }

        if (!$enrutado) {
          return response()->json([
            'errors' => ['messages' => ['La ruta no existe']],
            'data' => null,
          ], 400);
        }

        $permisos = array_map('intval', $request->permisos);
        $enrutado->permisos = $permisos;
        $resp = $enrutado->save();

        return response()->json([
          'errors' => !($resp) ? ['messages' => ['Error al guardar los permisos']] : null,
          'data' => ($resp) ? 'Permisos guardados correctamente' : null,
        ], ($resp) ? 200 : 400);
        
      } catch (\Exception $e) {
        return response()->json([
          'errors' => ['messages' => ['Ocurrió un error con el ID']],
          'data' => null,
        ], 400);
      }
    } else {

      try {

        $enrutado = Enrutado::find($id);
        $listadoRoles = Rol::where('estado', '=', 1)->get();
        //Generamos el listado de opciones que iran en el select, con las opciones correspondientes seleccionadas
        $optionSelect = BaseController::htmlSelect($listadoRoles, 'id', 'nombre', 'estado', 1, $enrutado->permisos);
        session(['response_data' => ['enrutado' => $enrutado, 'selectRoles' => $optionSelect]]);

        $view = view('layers.enrutado.modificacion')->render();
        $view_modificado = str_replace('$href_listado', route('Listado Rutas'), $view);
        
        return $view_modificado;
      } catch (\Exception $e) {
        return view('layers.enrutado.modificacion');
      }
    }
  }
}
