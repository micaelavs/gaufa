<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{

  use HasFactory;

  protected $table = 'puestos';
  public $timestamps = true;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'id',
    'nombre',
    'created_at',
    'updated_at',
    'id_estado',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [];

  public static function listarPuestos($params = [])
  {

    $columns = 'id,nombre';

    $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 : $params['start'];
    $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 : $params['lenght'];
    $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' : $params['search'];

    $query = Puesto::select('id', 'nombre')->where('id_estado', '=', 1);
    $data = ListadoAjax::listAjax($columns, $query, $params);

    return $data;
  }

  public static function listarPuestosExcel($params = [])
  {

    $columns = 'id,nombre';

    $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 : $params['start'];
    $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? Puesto::count() : $params['lenght'];
    $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' : $params['search'];

    $query = Puesto::select('id', 'nombre')->where('id_estado', '=', 1);
    $data = ListadoAjax::listAjax($columns, $query, $params);

    return $data;
  }
  
   public static function lista_select()
    {
        $puestos = Puesto::where('id_estado', 1)->get();
        return $puestos;
    }
}
