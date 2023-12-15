<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Firebase\JWT\JWT;
use App\Mail\TokenDeRecuperacion;
use App\Models\Permiso;
use App\Models\Rol;
use Exception;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;

class UsuariosController extends Controller
{

  public function usuarios(Request $request){
    return view('layers.usuarios.listado');
  }

  public function usuariosAjax(Request $request){
    
    $token = Request::capture()->cookie('jwt_token');
    try {
      $request->merge(['idModulo' => config('api.config.idModuloApp')]);
      $queryString = http_build_query($request->all());
      $response = Http::withHeaders([
          'Authorization' => 'Bearer '.$token
      ])->get(config('api.config.endPointAPIPanel').'/listadoUsuarios',$queryString);
     
      if (!$response->failed()) {
        //Agrego una columna más procesada
        $resp = json_decode($response->body());
        $resp->data = BaseController::agregarInfoJson($resp->data, Rol::class,'obtenerRol','rol');
        return json_encode($resp);

      } else {
        return response()->json(['error' => $response->json()], 500);

      }
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
  }


  public function listado(Request $request)
  {

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
        'idModulo' => !empty($request->idModulo) ? $request->idModulo : 0
      ];
      $data =  Usuario::listarUsuarios($params);
      return response()->json($data);
      
  }

  public function getUsuario($request,$id)
  {
      $token = Request::capture()->cookie('jwt_token');
      $request->merge(['id' => $id]);
      $response = Http::withHeaders([
          'Authorization' => 'Bearer '.$token
      ])->get(config('api.config.endPointAPIPanel').'/getUsuario/'.$id);
     
      $resp = json_decode($response->body());
      return $resp->data;
  }

  public function modificacionPermiso(Request $request, $id)
  {
    if ($request->isMethod('post')) {

      $usuario = $this->getUsuario($request,$id);
      if (!$usuario->id) {
        return response()->json([
          'errors' => ['messages' => ['Usuario no existente']],
          'data' => null,
        ], 400);
      }
      $validator = Validator::make($request->all(), [
        'permiso' => 'required|exists:roles,id'
      ]);
      if ($validator->fails()) {
        return response()->json([
          'errors' => $validator->errors(),
          'data' => null,
        ], 400);
      }
      
      $resp = $this->setPermiso($usuario->id,config('api.config.idModuloApp'),$request->permiso);
      return $resp;

    } else {
      
      $usuario = $this->getUsuario($request,$id);
      $permiso = 0;
      foreach($usuario->modulos as $modulos){
        
        if ($modulos->id == config('api.config.idModuloApp')){
          $permiso = $modulos->permiso;
        }
      }
      
      $listadoRoles = Rol::where('estado', '=', 1)->get();
      $optionSelect = BaseController::htmlSelect($listadoRoles, 'id', 'nombre', 'estado', 1, ($permiso) ? $permiso : null);
      session(['response_data' => ['usuario' => $usuario, 'selectRoles' => $optionSelect]]);
      $view = view('layers.usuarios.modificacionPermiso')->render();
      $view_modificado = str_replace('$href_listado', route('Listado Usuarios'), $view);
      return $view_modificado;

    }
  }

  public function bajaPermiso(Request $request, $id){
    if ($request->isMethod('post')) {

    
      $usuario = $this->getUsuario($request,$id);
      if (!$usuario->id) {
        return response()->json([
          'errors' => ['messages' => ['Usuario no existente']],
          'data' => null,
        ], 400);
      }

      $resp = $this->setPermiso($usuario->id,config('api.config.idModuloApp'),null);
      return $resp;

    } else {

      $usuario = $this->getUsuario($request,$id);
      if($usuario->id){
        $view = view('layers.usuarios.bajaPermiso')->render();
        $view_modificado = str_replace(
          ['$href_listado','$texto_baja'], 
          [route('Listado Usuarios'),'Está seguro que desea bajar el permiso para el usuario <b>'.$usuario->username.'</b>'], 
          $view);
        return $view_modificado;
      }else{
        return route('Listado Usuarios');
      }

    }
  }



  public function setPermiso($id_usuario, $id_modulo, $permiso)
  {
    $token = Request::capture()->cookie('jwt_token');
    if ($permiso != null){
      
      $response_user = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
        ])->post(config('api.config.endPointPanel') . '/setPermiso/'.$id_usuario.'/'.$id_modulo,["permiso"=>$permiso]);
        return response()->json([
          'errors' => (!$response_user) ? ['messages' => ['Error al guardar el permiso']] : null,
          'data' => ($response_user) ? 'Permiso guardado correctamente' : null,
      ], ($response_user) ? 200 : 400);
    }else{
      $response_user = Http::withHeaders([
        'Authorization' => 'Bearer ' . $token
        ])->post(config('api.config.endPointPanel') . '/eliminarPermiso/'.$id_usuario.'/'.$id_modulo);
        return response()->json([
          'errors' => (!$response_user) ? ['messages' => ['Error al borrar el usuario']] : null,
          'data' => ($response_user) ? 'permiso borrado correctamente' : null,
      ], ($response_user) ? 200 : 400);
    }
  }

}
