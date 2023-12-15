<?php

namespace App\Http\Controllers;

use App\Http\Helper\LoginApi;
use App\Models\PeticionUsuario;
use App\Models\Puesto;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\confirmacionPeticion;
use Illuminate\Support\Facades\Redis;


class PeticionUsuariosController extends Controller
{
	
  public function alta(Request $request)
  {

    if ($request->isMethod('post')) {

      Validator::extend('email_autorizado', function ($attribute, $value, $parameters, $validator) {
        // Lista de dominios permitidos
        $allowedDomains = ['fase.gob.ar', 'transporte.gob.ar'];
        // Extraer el dominio del correo electrónico
        $emailDomain = substr(strrchr($value, "@"), 1);
        // Verificar si el dominio del correo electrónico está en la lista de dominios permitidos
        return in_array($emailDomain, $allowedDomains);
      });

      //Ya hay solicitud preexistente con estado '4' -->'PENDIENTE DE APROBACION' o '5'---> 'APROBADA' o'6'--> 'RECHAZADA
     Validator::extend('peticion_activa', function ($attribute, $value, $parameters, $validator) {
      
      // Lista de dominios permitidos
      if($attribute=="email"){
        $peticion = PeticionUsuario::where('email','=',$value)->where('id_estado','>',3)->first();
        if($peticion){
          $estadoNombre = $peticion->estado->nombre;
          $email = $value;
          $fecha = $peticion->updated_at->format('d-m-Y H:i:s');
          // Configura el mensaje personalizado
          $validator->addReplacer('peticion_activa', function ($message, $attribute, $rule, $parameters) use ($estadoNombre,$email, $fecha) {
              return str_replace([':estado',':email', ':fecha'], [$estadoNombre,$email, $fecha], $message);
          });
          return false;
        }

       }

        return true;
      });

      //Ya hay una solicitud preexistente con estado pendiente de aprobacion
      Validator::extend('peticion_pendiente_aprobacion', function ($attribute, $value, $parameters, $validator) {
      if($attribute=="email"){
        $peticion = PeticionUsuario::where('email','=',$value)->where('id_estado','=',4)->first();
        //si hay alguna, muestro el mensaje de error por pantalla y luego envio mail avisando nuevamente a rrhh que debe atender la solictud 
        if($peticion){
          $email = $value;
          // Configura el mensaje personalizado
          $validator->addReplacer('peticion_pendiente_aprobacion', function ($message, $attribute, $rule, $parameters) use ($email) {
              return str_replace([':email'], [$email], $message);
          });
          return false;
        }

       }
        return true;
      });

       //Ya hay una solicitud preexistente aprobada
      Validator::extend('peticion_aprobada', function ($attribute, $value, $parameters, $validator) {
      if($attribute=="email"){
        $peticion = PeticionUsuario::where('email','=',$value)->where('id_estado','=',5)->first();
        //si hay alguna, muestro el mensaje de error por pantalla y luego envio un mail al rol operaciones para que asigne permisos
        if($peticion){
          $email = $value;
          // Configura el mensaje personalizado
          $validator->addReplacer('peticion_aprobada', function ($message, $attribute, $rule, $parameters) use ($email) {
              return str_replace([':email'], [$email], $message);
          });
          return false;
        }

       }
        return true;
      });

       Validator::extend('usuario_registrado', function ($attribute, $value, $parameters, $validator) {
      
      if($attribute=="email"){
          $email = $value;
          $loginApi = new LoginApi();
          $token = $loginApi->getToken();
          $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])->get(config('api.config.endPointAPIPanel') . '/getUsuarioPorCampo/email/' . $email); 

          	//caso la api no conecta
          	if($response->status()==500){
          		$validator->addReplacer('usuario_registrado', function ($message, $attribute, $rule, $parameters) use ($email) {       
          		$text = 'Hubo un error al conectar a la API del sistema, vuelva a intentarlo.';
              	return str_replace(':text', $text, $message);
          	});
          		return false;

          	//caso la api conecta, verifico si el usuario está registrado en newpanel
          	}elseif($response->status()==200){
      		   if(!empty($response["data"])){ 
	          		$validator->addReplacer('usuario_registrado', function ($message, $attribute, $rule, $parameters) use ($email) {
	          		$text = 'Ya existe un Usuario registrado en el sistema con el correo: <strong>'.$email.'</strong>';
	              	return str_replace(':text', $text, $message);
          			});

          		return false;

	          	}else{
	          		return true;
	          	}

          	}		

       }

        return true;
      });

      $rules = [
        'email' => [
          'required',
          'email_autorizado',
          'peticion_activa',
          'peticion_pendiente_aprobacion',
          'peticion_aprobada', 
          'usuario_registrado',

        ],
        'dni' => ['required', 'regex:/^[0-9]{7,8}$/'],
        'apellido' => 'required|string|max:20',
        'nombre' => 'required|string|max:20',
        'puesto' => 'required|numeric',
        'area' => 'required|numeric',
        'password' => [
          'required',
          'string',
          'min:8',
          'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[_])/'
        ],
        'confirmacion_password' => [
          'required',
          'string',
          'min:8',
          'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[_])/',
          'same:password'
        ],
      ];

      $messages = [
        'email.required' => 'El campo mail es requerido.',
        'email.email' => 'El campo mail debe ser un tipo de cuenta de email.',
        'password.regex' => 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un guión bajo.',
        'dni.regex' => 'El número de DNI debe ser válido y tener 7 u 8 dígitos numéricos.',
        'email.ends_with' => 'El correo electrónico debe tener el dominio @fase.gob.ar.',
        'confirmacion_password.regex' => 'La confirmación de password debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un guión bajo.',
        'confirmacion_password.same' => 'La Confirmación de contraseña no coincide con el Password ingresado.',
        'email.email_autorizado' => 'El email ingresado no está autorizado para ser usuario del sistema.',
        'email.peticion_activa' => 'Ya existe una petición activa para el email <strong>:email</strong> con estado <strong>:estado</strong> solicitada el <strong>:fecha</strong>.',
        'email.usuario_registrado' => ':text',
        'email.peticion_pendiente_aprobacion' => 'Tiene una petición pendiente <strong>POR APROBAR</strong> para el email <strong>:email</strong>. Se ha reenviado un correo a RRHH para que atiendan su solicitud.',
        'email.peticion_aprobada'=> 'Tiene una asignación <strong>Pendiente por asignar permisos</strong> de usuario. Se ha enviado un mail al sector operaciones para que atienda la solicitud.'
      ];

      $validator = Validator::make($request->all(), $rules, $messages);


      if ($validator->fails()) {
      	//SOLICITUD PENDIENTE DE APROBACION = 4
      	 $peticion = PeticionUsuario::where('email','=',$request->email)->where('id_estado','=',4)->first();
        //envio mail rrhh
        if ($peticion) {
         	try {
         		$email = $peticion->email;
         		$nombre_completo = $peticion->nombre. ' ' .$peticion->apellido;
         		$dni = $peticion->dni;
         		$texto = "AVISO: Tiene pendiente una solicitud pendiente por APROBAR del usuario: ".$nombre_completo. " - DNI: ".$dni. " - email: ".$email;
         		session(['response_data' => ['texto' => $texto]]);	
			    $destinatario_rrhh = config('api.config.destinatario_rrhh'); //mail rrhh
			    Mail::to($destinatario_rrhh)->send(new confirmacionPeticion($texto));
			    } 

		    catch (\Exception $e) {

		    return response()->json([
		        'errors' => ['messages' => [(env('APP_DEBUG')) ? $e->getMessage() : 'Ocurrió un error al enviar el mail.']],
		        'data' => null,
		      ], 404);
		    }
		   //envío los errores para que se muestren por pantalla
		    return response()->json([
		      'errors' => $validator->errors(),
		      'data' => null,
		    ], 422);

		}
		//SOLICITUD APROBADA= 5
		$peticion = PeticionUsuario::where('email','=',$request->email)->where('id_estado','=',5)->first();
		if ($peticion) {
         	try {
         		$email = $peticion->email;
         		$nombre_completo = $peticion->nombre. ' ' .$peticion->apellido;
         		$dni = $peticion->dni;
         		$texto = "AVISO: Tiene una solicitud pendiente por asignar permisos del usuario: ".$nombre_completo. " - DNI: ".$dni. " - email: ".$email;
         		session(['response_data' => ['texto' => $texto]]);	
			    $destinatario_operaciones = config('api.config.destinatario_operaciones'); //mail operaciones
			    Mail::to($destinatario_operaciones)->send(new confirmacionPeticion($texto));
			    } 

		    catch (\Exception $e) {

		    return response()->json([
		        'errors' => ['messages' => [(env('APP_DEBUG')) ? $e->getMessage() : 'Ocurrió un error al enviar el mail.']],
		        'data' => null,
		      ], 404);
		    }
		   //envío los errores para que se muestren por pantalla
		    return response()->json([
		      'errors' => $validator->errors(),
		      'data' => null,
		    ], 422);

		}

        return response()->json([
          'errors' => $validator->errors(),
          'data' => null,
        ], 422);
       

      } else {

        $peticion = new PeticionUsuario();
        $peticion->email = $request->email;
        $peticion->dni = $request->dni;
        $peticion->apellido =  $request->apellido;
        $peticion->nombre =  $request->nombre;
        $peticion->id_puesto = $request->puesto;
        $peticion->id_area = $request->area;
        $peticion->password = bcrypt($request->password);
        $peticion->id_estado = 4;
        $resp = $peticion->save();

        if($resp){
        	//si se gestionó correcta la petición, envío mails
        	 try {

        	 	$email = $peticion->email;
         		$nombre_completo = $peticion->nombre. ' ' .$peticion->apellido;
         		$dni = $peticion->dni;
         		$texto = "AVISO: Se ha iniciado una solictud de alta de usuario para: ".$nombre_completo. " - DNI: ".$dni. " - email: ".$email;
         		session(['response_data' => ['texto' => $texto]]);
         		session(['response_data' => ['email' => $peticion->email]]);	
			    $destinatario_rrhh = config('api.config.destinatario_rrhh'); //mail rrhh
			    $destinatarios = [$email, $destinatario_rrhh];
			    Mail::to($destinatarios)->send(new confirmacionPeticion($texto));
			    } catch (\Exception $e) {

			      return response()->json([
			        'errors' => ['messages' => [(env('APP_DEBUG')) ? $e->getMessage() : 'Ocurrió un error al enviar el mail.']],
			        'data' => null,
			      ], 404);
			    }

			    return response()->json([
			      'errors' => null,
			      'data' => 'Se ha enviado un correo electrónico a ' . $destinatario_rrhh . 'para que atienda su solicitud.',
			    ], 200);


        }
        return response()->json([
          'errors' => !($resp) ? 'Error al guardar cargar la Petición' : null,
          'data' => ($resp) ? 'La Petición se ha cargado correctamente' : null,
        ], ($resp) ? 200 : 400);
      }

    } else {
      	$puestos = Puesto::lista_select();
      	$optionSelect_puestos = BaseController::htmlSelect($puestos, 'id', 'nombre', 'id_estado', 1);
      	$view = view('layers.peticionUsuarios.alta')->render();
      	$view_modificado = str_replace(['$href_volver','$_puestos', '$url_confirmacion'],[ route('Listado Peticiones'),$optionSelect_puestos, route('Confirmacion Peticion')], $view);
      return $view_modificado;

    }
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
      if ($request->search && preg_match('/^\d{2}\/\d{2}\/\d{4}/', $request->search['value'], $date)) {
        $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $request->search['value']);
        $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
      } else {
        $search = ($request->search) ? $request->search['value'] : null;
      }

      $params  = [
        'order'    => $orders,
        'start'    => !empty($tmp = $request->start)
          ? $tmp : 0,
        'lenght'  => !empty($tmp = $request->length)
          ? $tmp : 10,
        'search'  => !empty($search)
          ? $search : '',
          'filters'   =>  (array_key_exists('filters', $request->all())) 
           ? $request->filters : null ,
      ];
      $data =  PeticionUsuario::listarPeticionesUsuarios($params);
      return response()->json($data);
  }

  public function listado(Request $request){
    $estados = Estado::lista_select();
    $optionSelect_estados = BaseController::htmlSelect($estados, 'id', 'nombre');
    $view = view('layers.peticionUsuarios.listado')->render();
    $view_modificado = str_replace(['$href_nueva_peticion','$_estados'], [route('Alta Peticion'), $optionSelect_estados], $view);
    return $view_modificado;
    }

 public function confirmacion(Request $request){
 	$email = session('response_data')['email'];
 	$view = view('layers.peticionUsuarios.confirmacion')->render();
    $view_modificado = str_replace(['$href_aceptar', '$email'], [route('Alta Peticion'), $email], $view);
    return $view_modificado;
 }	

  public function rechazar(Request $request, $id){
  	
    if ($request->isMethod('post')) {
      $peticion = PeticionUsuario::find($id);
      if (empty($peticion)) {
        return response()->json([
          'errors' => 'La solicitud a rechazar no existe.',
          'data' => null,
        ], 422);
      }

      $peticion->id_estado = 6;
      $resp = $peticion->save();

      return response()->json([
        'errors' => !($resp) ? 'Error al rechazar la solicitud.' : null,
        'data' => ($resp) ? 'La solicitud ha sido Rechazada correctamente' : null,
      ], ($resp) ? 200 : 400);

    } else {

      $peticion = PeticionUsuario::find($id);
      if (empty($peticion)) {
        return route('Listado Peticiones');
      }
      $view = view('layers.peticionUsuarios.generica')->render();
      $view_modificado = str_replace(['$href_volver', '$texto'], [route('Listado Peticiones'), "Está por rechazar la solicitud de: <b>{$peticion->nombre}</b> <b>{$peticion->apellido}</b> Usuario: <b>{$peticion->email}</b>"], $view);
      return $view_modificado;
      
    }
  }

   public function aprobar(Request $request, $id){
    if ($request->isMethod('post')) {
      $peticion = PeticionUsuario::find($id);
      if (empty($peticion)) {
        return response()->json([
          'errors' => 'La solicitud a aprobar no existe.',
          'data' => null,
        ], 422);
      }

      	  $token = Request::capture()->cookie('jwt_token'); 
	      $partes =explode("@", $peticion->email);
	      $usuario = $partes[0];
	      $response = Http::withHeaders([
	      'Authorization' => 'Bearer ' . $token])->post(config('api.config.endPointAPIPanel').'/addUsuario', [
	      'username' => $usuario,
	      'nombre' => $peticion->nombre,
	      'apellido' => $peticion->apellido,
	      'documento' => $peticion->dni,
	      'email' => $peticion->email,
	      'password' =>  $peticion->password,
	      'is_user_api' => 0,
	      'id_area' => $peticion->id_area,
	      'id_puesto' => $peticion->id_puesto,
	      'id_modulo' => config('api.config.id_modulo_app')
	    	]); 

	      $result = $response->json();
	      if($response->status()==422){
	      	 return response()->json([
	          'errors' => $result["errors"],
	          'data' => null,
	        ], 422);
	      }

	      if($response->status()==200){
	      	//se guarda en panel y recien ahi le cambio el estado a aceptado en tabla peticiones
			$peticion->id_estado = 5;
      		$peticion->save();
      		//envio mail al usuario notificando que se aprobó su solicitud
      		try {
	      		$email = $peticion->email;
	     		$texto = "AVISO: Se ha APROBADO la solicitud de su alta se Usuario, Email: ".$email . ". Deberá aguardar por la asignación de permisos por el sector de Operaciones.";
	     		session(['response_data' => ['texto' => $texto]]);
	     		session(['response_data' => ['email' => $peticion->email]]);	
			    Mail::to($email)->send(new confirmacionPeticion($texto));
			    } 
			catch (\Exception $e) {
		      	return response()->json([
			        'errors' => ['messages' => [(env('APP_DEBUG')) ? $e->getMessage() : 'Ocurrió un error al enviar el mail.']],
			        'data' => null,
			      	], 404);
			    }

		    return response()->json([
		      'errors' => null,
		      'data' => 'Se ha enviado un correo electrónico a ' . $email. 'avisando la confirmación y aceptación de la solicitud.',
		    ], 200);
	  	    
	  	    return response()->json([
		      'errors' => null,
		      'data' => 'Se han guardado correctamente los datos del usuario: ' . $peticion->email. 'en el sistama.',
			], 200);
	      }
      
    } else {

      $peticion = PeticionUsuario::find($id);
      if (empty($peticion)) {
        return route('Listado Peticiones');
      }
      $view = view('layers.peticionUsuarios.generica')->render();
      $view_modificado = str_replace(['$href_volver', '$texto'], [route('Listado Peticiones'), "Está por aprobar la solicitud de: <b>{$peticion->nombre}</b> <b>{$peticion->apellido}</b> Usuario: <b>{$peticion->email}</b>"], $view);
      return $view_modificado;
      
    }
  }
  	public function modificacion(Request $request, $id){

  		if ($request->isMethod('post')) {
	      
		    $validator = Validator::make($request->all(), [
		        'puesto' => 'required|numeric',
		        'area' => 'required|numeric'
		    ]);

	    if ($validator->fails()) {
	        return response()->json([
	          'errors' => $validator->errors(),
	          'data' => null,
	        ], 422);
	    }

	    $peticion = PeticionUsuario::find($id);
      	if (empty($peticion)) {
        	return response()->json([
          	'errors' => ['messages' => ['La Petición a modificar no existe.']],
          	'data' => null,
        	], 422);
      	}

	    $peticion->id_puesto = $request->puesto;
	    $peticion->id_area = $request->area;
	    $resp = $peticion->save();

	    return response()->json([
	        'errors' => !($resp) ? ['messages' => ['Error al guardar la Petición']] : null,
	        'data' => ($resp) ? 'La Petición se ha guardado correctamente' : null,
	      ], ($resp) ? 200 : 400);

    	}else{
			$peticion = PeticionUsuario::find($id);
	      	session(['response_data' => ['peticion' => $peticion]]);
	    	$puestos = Puesto::lista_select();
	  		$optionSelect_puestos = BaseController::htmlSelect($puestos, 'id', 'nombre', 'id_estado', 1);
	  		$view = view('layers.peticionUsuarios.modificacion')->render();
	  		$view_modificado = str_replace(['$href_volver','$_puestos', '$url_listado'],[ route('Listado Peticiones'),$optionSelect_puestos, route('Listado Peticiones')], $view);
	  		return $view_modificado;
    	}

    
	}

	

}
