<?php

namespace App\Models;


class ListadoAjax
{

  protected $columns    = null;
  protected static $FLAG  = false;

  public static function listAjax($columns_search, $query, $params = array(), $debug = false)
  {


    $recordsTotal = $query->get()->count();
    $columns_array = explode(',', $columns_search);
    if (!empty($params['search'])) {
      $init = true;
      $text = (string)$params['search'];
      foreach ($columns_array as $column) {
        ($init) ? $query->where($column, 'like', '%' . $text . '%') : $query->orWhere($column, 'like', '%' . $text . '%');
        $init = false;
      }
    }
    $recordsFiltered = $query->get()->count();

    /*Orden de las columnas */
    foreach ($params['order'] as $i => $val) {
      if (isset($val['dir']) && in_array($val['dir'], ['asc', 'desc']))
        $query->orderBy($val['column'], $val['dir']);
    }

    /**Limit: funcionalidad: desde-hasta donde se pagina */
    if (isset($params['lenght']) && isset($params['start']))
      $query->offset($params['start'])->limit($params['lenght']);

    $list           = json_decode($query->get()->toJson(), true);

    if ($list) {
      foreach ($list as $key => $value) {
        foreach ($value as $ke => $val) {
          if (preg_match('/^\d{4}\-\d{2}\-\d{2}.*/', $val)) {
            /*quito hora minuto y segundo*/
            $value[$ke] = date('d/m/Y H:i:s', strtotime($val));
          }
        }
        $value  = (object)$value;
      }
    }
    if ($debug) {
      dd('recordsTotal:' . $recordsTotal);
      dd('recordsFiltered:' . $recordsFiltered);
      dd('data', $list);
      die;
    }
    return [
      'recordsTotal'    => !empty($recordsTotal) ? $recordsTotal : 0,
      'recordsFiltered' => !empty($recordsFiltered) ? $recordsFiltered : 0,
      'data'            => $list ? $list : [],
    ];
  }
}
