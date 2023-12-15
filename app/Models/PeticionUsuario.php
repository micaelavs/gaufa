<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeticionUsuario extends Model
{
    use HasFactory;

    protected $table = 'peticion_usuarios';
	public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'email',
        'dni',
        'apellido',
        'nombre',
        'id_puesto',
        'id_area',
        'password',
        'id_estado',
        'created_at',
        'updated_at'
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime:d/m/Y H:i:s'
    ];

    public function estado(){ 
        return $this->belongsTo(Estado::class, 'id_estado'); 
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class, 'id_puesto');
    }

    public static function listarPeticionesUsuarios($params = []){
      $columns = 'id, updated_at, email, dni, apellido, nombre, puesto, id_area, id_estado';

      $params['start']  = (!isset($params['start'])  || empty($params['start']) )  ? 0 :$params['start'];
      $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght']) ) ? 10 :$params['lenght'];
      $params['search'] = (!isset($params['search']) || empty($params['search']) ) ? '' :$params['search'];

      $query = PeticionUsuario::select('peticion_usuarios.id', 'email', 'dni', 'apellido', 'peticion_usuarios.nombre', 'puestos.nombre as puesto', 'id_area', 'peticion_usuarios.id_estado', 'peticion_usuarios.updated_at')
        ->leftJoin('puestos','peticion_usuarios.id_puesto','=','puestos.id')
        ->where('peticion_usuarios.id_estado','>=',3)
        ->orderBy('peticion_usuarios.id_estado', 'desc');
        
      //filtros
        if (!empty($params['filters'])) {
            if (!empty($params['filters']['estado'])) {
              $query->where('peticion_usuarios.id_estado','=',$params['filters']['estado']);
            }
        }


      $data = ListadoAjax::listAjax($columns, $query, $params);

      return $data;
    }
}
