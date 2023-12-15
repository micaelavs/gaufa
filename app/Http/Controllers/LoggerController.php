<?php

namespace App\Http\Controllers;

use App\Models\Logger;
use Illuminate\Http\Request;

class LoggerController extends Controller
{
    public function listado(Request $request){
        return view('layers.auditorias.listado');
    }

    public function listadoAjax(Request $request){
       //Listado por ajax
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
         'filters'   =>  (array_key_exists('filters', $request->all())) 
            ? $request->filters : null ,
       ];

       $data =  Logger::listarLogs($params);
       return response()->json($data);
    }
}
