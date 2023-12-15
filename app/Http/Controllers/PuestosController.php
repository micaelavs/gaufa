<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



class PuestosController extends Controller
{
  public function listado(Request $request)
  {

    if ($request->isMethod('post')) {

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
        $search = ($request->search)? $request->search['value']:null;
      }
      $params  = [
        'order'    => $orders,
        'start'    => !empty($tmp = $request->start)
          ? $tmp : 0,
        'lenght'  => !empty($tmp = $request->length)
          ? $tmp : 10,
        'search'  => !empty($search)
          ? $search : '',
        'filters'   => [],
      ];
      $data =  Puesto::listarPuestos($params);
      return Response::json($data, 200);
    
    }else{
      $view = view('layers.puestos.index')->render();
      $view_modificado = str_replace(['$href_alta_puesto', '$href_listadoExcel'], [route('Alta Puesto'), route('Listado Excel')], $view);
      return $view_modificado;
    }
  }

  public function listadoExcel(Request $request)
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
      if ($request->search && preg_match('/^\d{2}\/\d{2}\/\d{4}/', $request->search['value'], $date)) {
        $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $request->search['value']);
        $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
      } else {
        $search = ($request->search)? $request->search['value']:null;
      }

      $params  = [
        'order'    => $orders,
        'start'     => '',
        'length'    => '',
        'search'  => !empty($search) ? $search : '',
        'filters'   => [],
      ];
      
      $data =  Puesto::listarPuestosExcel($params);
      $datosPuestos= $data['data'];  

      $spreadsheet = new Spreadsheet();

      $sheet = $spreadsheet->getActiveSheet();
      $sheet->setTitle('Puestos');

      $columnas = ['Nombre'];
      $columna = 1;
      foreach ($columnas as $titulo) {
          $sheet->setCellValueByColumnAndRow($columna, 1, $titulo);
          $columna++;
      }

      $fila = 2;
      foreach ($datosPuestos as $puesto) {
          $sheet->setCellValueByColumnAndRow(1, $fila, $puesto['nombre']);
          $fila++;
      }

      $writer = new Xlsx($spreadsheet);

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="listado_puestos.xlsx"');
      header('Cache-Control: max-age=0');

      $writer->save('php://output');
  }

  public function alta(Request $request)
  {
    if ($request->isMethod('post')) {
      $puesto = Puesto::where('nombre', $request->nombre)->first();
      
      if ($puesto && $puesto->id_estado == 2) {
        $puesto->id_estado = 1;
        $resp = $puesto->save();
      } else {

        $validator = Validator::make($request->all(), [
          'nombre' => 'required|string|max:255|unique:puestos',
        ]);

        if ($validator->fails()) {
          return response()->json([
            'errors' => $validator->errors(),
            'data' => null,
          ], 422);
        }

        $puesto = new Puesto();
        $puesto->nombre = $request->nombre;
        $puesto->id_estado = 1;
        $resp = $puesto->save();
      }

      return response()->json([
        'errors' => !($resp) ? 'Error al guardar el puesto' : null,
        'data' => ($resp) ? 'Puesto guardado correctamente' : null,
      ], ($resp) ? 200 : 400);
    } else {

      $view = view('layers.puestos.alta')->render();
      $view_modificado = str_replace('$href_volver', route('listado-puestos'), $view);
      return $view_modificado;
    }
  }

  public function modificacion(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $validator = Validator::make($request->all(), [
        'nombre' => 'required|string|max:255|unique:puestos,nombre,' . $id,
      ]);

      if ($validator->fails()) {
        return response()->json([
          'errors' => $validator->errors(),
          'data' => null,
        ], 422);
      }

      $puesto = Puesto::find($id);
      if (empty($puesto)) {
        return response()->json([
          'errors' => 'El puesto a modificar no existe.',
          'data' => null,
        ], 422);
      }

      $puesto->nombre = $request->nombre;
      $resp = $puesto->save();

      return response()->json([
        'errors' => !($resp) ? 'Error al guardar el Puesto' : null,
        'data' => ($resp) ? 'Puesto guardado correctamente' : null,
      ], ($resp) ? 200 : 400);
    } else {

      $puesto = Puesto::find($id);
      session(['response_data' => ['puesto' => $puesto]]);
      $view = view('layers.puestos.modificacion')->render();
      $view_modificado = str_replace('$href_volver', route('listado-puestos'), $view);
      return $view_modificado;
    }
  }

  public function baja(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $puesto = Puesto::find($id);
      if (empty($puesto)) {
        return response()->json([
          'errors' => 'El puesto a dar de baja no existe.',
          'data' => null,
        ], 422);
      }

      $puesto->id_estado = 2;
      $resp = $puesto->save();

      return response()->json([
        'errors' => !($resp) ? 'Error al borrar el Puesto' : null,
        'data' => ($resp) ? 'Puesto dado de baja correctamente' : null,
      ], ($resp) ? 200 : 400);

    } else {

      $puesto = Puesto::find($id);
      if (empty($puesto)) {
        return route('listado-puestos');
      }
      $view = view('layers.puestos.baja')->render();
      $view_modificado = str_replace(['$href_volver', '$texto_baja'], [route('listado-puestos'), "Usted está por borrar el Puesto <b>{$puesto->nombre}</b>"], $view);
      return $view_modificado;
      
    }
  }
}
