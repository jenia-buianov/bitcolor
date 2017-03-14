<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   if(Auth::user()) return redirect('game');
    else return view('welcome');
});


Route::auth();

Route::get('game','GameController@listBets');
Route::post('game','GameController@listGames');
Route::post('observer', 'GameController@observer');

Route::get('game/active','GameController@getActiveGames');
Route::post('game/active','GameController@postActiveGames');

Route::get('game/create','GameController@createGame');
Route::get('game/unset','GameController@unsetFinishedGames');
Route::get('game/view/{id}', 'GameController@getViewGame')->where('id', '[0-9]+');
Route::post('game/view/{id}', 'GameController@postViewGame')->where('id', '[0-9]+');
Route::post('game/bet','GameController@bet');

Route::post('notifications/seen','NotificationsController@seenNotification');



