<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'username',
        'nombre',
        'apellido',
        'documento',
        'email',
        'id_dependencia',
        'estado',
        'is_user_super',
        'reset_password',
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'is_user_api',
        'password',
        'token_password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * RELACIONES ENTRE MODELOS
    */
    public function permisos()
    {
        return $this->hasMany(Permiso::class,'id_usuario');
    }

    public function modulos()
    {
      return $this->belongsToMany(Modulo::class, 'permisos','id_usuario','id_modulo')->where('permisos.estado','=',1)->where('modulos.id','!=',config('api.idModuloApp'));
    }

    /**
   * MÉTODOS
   */

    public function permisoPorModulo($id_modulo){
      return $this->permisos()->where('id_modulo', $id_modulo)->where('estado', 1)->first();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function listarUsuarios($params = []){
      $columns = 'id,username,nombre,apellido,documento,email,id_dependencia';

      $params['start']  = (!isset($params['start'])  || empty($params['start']) )  ? 0 :$params['start'];
      $params['length'] = (!isset($params['length']) || empty($params['length']) ) ? 10 :$params['length'];
      $params['search'] = (!isset($params['search']) || empty($params['search']) ) ? '' :$params['search'];

      $query = self::select( 'id','username','nombre', 'apellido', 'documento', 'email','id_dependencia')->where('estado','=',1)->with(['permisos' => function ($query) use ($params) {
        $query->where('id_modulo', '=', $params['idModulo'])->where('estado', '=', 1);
      }]);
      $data = ListadoAjax::listAjax($columns, $query, $params);

      return $data;
    }
    
}
