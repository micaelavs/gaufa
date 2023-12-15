<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Logger extends Model
{
  use HasFactory;

  protected $table = 'loggers';
  public $timestamps = false;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'id',
    'model',
    'record_id',
    'action',
    'old_data',
    'new_data',
    'user',
    'created_at',
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
  protected $casts = [
    'old_data' => 'json',
    'new_data' => 'json',
    'user' => 'json',
    'created_at' => 'datetime:d/m/Y H:i:s',
  ];



  public static function listarLogs($params = [])
  {
    $columns = 'model,record_id,action,old_data,new_data,user,created_at';

    $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 : $params['start'];
    $params['length'] = (!isset($params['length']) || empty($params['length'])) ? 10 : $params['length'];
    $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' : $params['search'];
    $query = self::select('model', 'record_id', 'action', 'old_data', 'new_data', 'user', 'created_at');

    if (!empty($params['filters'])) {
      if (!empty($params['filters']['fecha_desde'])) {
          $fecha = Carbon::createFromFormat('d/m/Y', $params['filters']['fecha_desde'])->setTime(0, 0, 0);
          $query->where('created_at','>=',$fecha);
      }
      if (!empty($params['filters']['fecha_hasta'])) {
        $fecha = Carbon::createFromFormat('d/m/Y', $params['filters']['fecha_hasta'])->setTime(23, 59, 59);
        $query->where('created_at','<=',$fecha);
      }
    }

    $data = ListadoAjax::listAjax($columns, $query, $params);

    return $data;
  }
}
