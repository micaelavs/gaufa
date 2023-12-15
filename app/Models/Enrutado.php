<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Enrutado extends Model
{
    use HasFactory;

    protected $table = 'enrutado';
    public $incrementing = false;
	  public $timestamps = true;
    protected $primaryKey = 'nombre';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'url',
        'nombre',
        'accion',
        'permisos',
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
        'permisos' => 'array'
    ];
    
    public static function listarRutas($params = []){

      $columns = 'id,enrutado.nombre,accion';

      $params['start']  = (!isset($params['start'])  || empty($params['start']) )  ? 0 :$params['start'];
      $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght']) ) ? 10 :$params['lenght'];
      $params['search'] = (!isset($params['search']) || empty($params['search']) ) ? '' :$params['search'];

      $query = DB::table('enrutado')
      ->select('enrutado.nombre as id','enrutado.nombre','enrutado.accion', DB::raw("GROUP_CONCAT(CONCAT(roles.nombre, ' ') SEPARATOR ', ') as roles_permisos"))
      ->leftJoin('roles', function ($join) {
          $join->on('enrutado.permisos', 'like', DB::raw("CONCAT('%[', roles.id , ',%')"))
          ->orWhere('enrutado.permisos', 'like', DB::raw("CONCAT('%,', roles.id , ',%')"))
          ->orWhere('enrutado.permisos', 'like', DB::raw("CONCAT('%', roles.id , ']%')"));
      })
      ->where('enrutado.estado', '=', 1)
      ->groupBy('enrutado.nombre','enrutado.accion');

      $data = ListadoAjax::listAjax($columns, $query, $params);

      return $data;

    }

}
