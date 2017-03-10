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
//Route::get('/home', 'HomeController@index');

Route::get('game','GameController@listBets');
Route::post('observer', 'GameController@observer');
Route::get('observer', 'GameController@observer');
//Route::match(['get', 'post'], '/game/{bet}', 'GameController@bet');

// Route::post('/game', 'GameController@bet');

    /*$validator = Validator::make($request->all(), [
        'players' => 'required|max:255',
    ]);

    if ($validator->fails()) {
        return redirect('/')
            ->withInput()
            ->withErrors($validator);
    }*/




