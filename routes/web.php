<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
Route::get('/', function () {
    return view('welcome');
});
*/
use App\User;

Route::get('/', function(){
    return view('inicio');
});
Route::get('usuarios', function(){
	//$users = User::all();
	$consultores = User::join('permissao_sistema','cao_usuario.co_usuario','=','permissao_sistema.co_usuario')
	   ->where('co_sistema',1)
	   ->where('in_activo','S')
	   ->whereBetween('co_tipo_usuario',[0,1,2])
	   ->get();
	  return response()->json($consultores);
});


Route::get('/relatorio', 'DesempenhoController@getRelatorio');
Route::get('/pie_chart', 'DesempenhoController@getPiechart');
Route::get('/bar_chart', 'DesempenhoController@getBarchart');
