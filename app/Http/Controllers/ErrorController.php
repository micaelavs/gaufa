<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ErrorController extends Controller
{
    public function error(Request $request){
      $view = view('errors.error')->render();
      $error = session('error_response');
      Session::flush();
      $view_modificado = str_replace(['$_redirec_home', '$_error'], [route('home'), $error], $view);
      return $view_modificado;
    }
}
