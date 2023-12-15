<?php

use App\Http\Controllers\PeticionUsuariosController;
use App\Http\Controllers\PuestosController;
use App\Http\Controllers\webController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EnrutadoController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\LoggerController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Anotaciones: se utiliza webController cuando la logica no es compleja ni corresponde a algún controlador
|
*/

//Middleware de Auth JWT, ActualizarEnrutado y Chequeo de Rol (Permiso)
Route::middleware(['CookieToken'])->group(function () {

  //Pagina de error
  Route::get('/error', [ErrorController::class, 'error'])->name('error');
  //Pagina Principal
  Route::match(['get', 'post'], '/', [webController::class, 'home'])->name('home');
  //Formulario de Alta de Usuario
  Route::match(['get', 'post'], '/peticionUsuario', [PeticionUsuariosController::class, 'alta'])->name('Alta Peticion');
  //Datos de la aplicacion 
  Route::get('/dataApp', [webController::class, 'dataApp'])->name('dataApp');

  //Middleware de Auth JWT, ActualizarEnrutado y Chequeo de Rol (Permiso)
  Route::middleware(['ActualizarEnrutadoMiddleware', 'JwtMiddleware', 'RolPermisoMiddleware'])->group(function () {

    //Vista de Puestos
    Route::match(['get', 'post'], '/puestos/listado', [PuestosController::class, 'listado'])->name('Listado Puestos');
    Route::match(['get', 'post'], '/puestos/alta', [PuestosController::class, 'alta'])->name('Alta Puesto');
    Route::match(['get', 'post'], '/puestos/baja/{id}', [PuestosController::class, 'baja'])->name('Baja Puesto');
    Route::match(['get', 'post'], '/puestos/modificacion/{id}', [PuestosController::class, 'modificacion'])->name('Modificacion Puesto');
    Route::match(['get', 'post'], '/puestos/listadoExcel', [PuestosController::class, 'listadoExcel'])->name('Listado Excel');

    //====ROLES====
    Route::get('/rol/listado', [RolController::class, 'listado'])->name('Listado Roles');
    Route::get('/rol/listadoAjax', [RolController::class, 'listadoAjax'])->name('Ajax Roles');
    Route::match(['get', 'post'], '/rol/alta', [RolController::class, 'alta'])->name('Agregar Rol');
    Route::match(['get', 'post'], '/rol/modificacion/{id}', [RolController::class, 'modificacion'])->name('Modificar Rol');
    Route::match(['get', 'post'], '/rol/baja/{id}', [RolController::class, 'baja'])->name('Eliminar Rol');

    //====PERMISOS====
    Route::get('/enrutado/listado', [EnrutadoController::class, 'listado'])->name('Listado Rutas');
    Route::get('/enrutado/listadoAjax', [EnrutadoController::class, 'listadoAjax'])->name('Ajax Rutas');
    Route::match(['get', 'post'], '/enrutado/modificacion/{id}', [EnrutadoController::class, 'modificacion'])->name('Modificar Ruta');

    //====LOGGERS====
    Route::get('/loggers/listado', [LoggerController::class, 'listado'])->name('Listado Auditorias');
    Route::get('/loggers/listadoAjax', [LoggerController::class, 'listadoAjax'])->name('Ajax Auditorias');

    Route::get('/usuarios/listado', [UsuariosController::class, 'usuarios'])->name('Listado Usuarios');
    Route::get('/usuarios/listadoAjax', [UsuariosController::class, 'usuariosAjax'])->name('Ajax Usuarios');
    Route::match(['get', 'post'], '/usuarios/modificacionPermiso/{id}', [UsuariosController::class, 'modificacionPermiso'])->name('Modificacion Permiso');
    Route::match(['get', 'post'], '/usuarios/bajaPermiso/{id}', [UsuariosController::class, 'bajaPermiso'])->name('Baja Permiso');

    //====PETICIONES====
    Route::match(['get'], '/peticionUsuario/listado', [PeticionUsuariosController::class, 'listado'])->name('Listado Peticiones');
    Route::match(['get'], '/peticionUsuario/listadoAjax', [PeticionUsuariosController::class, 'listadoAjax'])->name('Ajax Peticiones');
    Route::match(['get', 'post'], '/peticionUsuario/confirmacion', [PeticionUsuariosController::class, 'confirmacion'])->name('Confirmacion Peticion');
    Route::match(['get', 'post'], '/peticionUsuario/rechazar/{id}', [PeticionUsuariosController::class, 'rechazar'])->name('Rechazar Peticion');
    Route::match(['get', 'post'], '/peticionUsuario/aprobar/{id}', [PeticionUsuariosController::class, 'aprobar'])->name('Aprobar peticion');
    Route::match(['get', 'post'], '/peticionUsuario/modificacion/{id}', [PeticionUsuariosController::class, 'modificacion'])->name('Modificacion Peticion');
  });
  
});

//Aplico solo JwtMiddleware
Route::middleware(['JwtMiddleware'])->group(function () {
  Route::get('/logout', [webController::class, 'logout'])->name('logout');
});
