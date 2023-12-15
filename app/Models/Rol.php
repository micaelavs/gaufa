<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
	  public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];

    public static function listarRoles($params = []){

      $columns = 'id,nombre,descripcion,estado';

      $params['start']  = (!isset($params['start'])  || empty($params['start']) )  ? 0 :$params['start'];
      $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght']) ) ? 10 :$params['lenght'];
      $params['search'] = (!isset($params['search']) || empty($params['search']) ) ? '' :$params['search'];

      $query = self::select( 'id', 'nombre','descripcion','estado')->where('estado','=',1);
      $data = ListadoAjax::listAjax($columns, $query, $params);

      return $data;
    }

    public function obtenerRol($usuario){
      if(!empty($usuario->permisos)){
        $rol = Rol::select('nombre')->where('id',$usuario->permisos[0]->permiso)->first();
        if($usuario->super){ return 'Super Usuario'; }
        if(!$rol){ return 'Sin Rol'; }
        return $rol->nombre;
      }
      return 'Sin Rol';
    }
}
