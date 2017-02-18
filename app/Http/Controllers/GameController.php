<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Bet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function bet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:255',
            'amount' => 'required|max:255',
            'sector' => 'required|max:255',
        ]);


        if ($validator->fails()) {
            return redirect('/game')
                ->withInput()
                ->withErrors($validator);

        }
//        dd($validator);
        $bet = new Bet;
        $amount = $request->amount;
        $email = Auth::user()->email;
        $sector = $request->sector;
        $game_id = '1';

        $bet->game_id = $game_id;
        $bet->amount = $amount;
        $bet->email = $email;
        $bet->sector = $sector;
        $bet->save();
        return redirect('/game');
    }
//        dd($bets);
        /*foreach ($bets as $bet){
        echo($bet);
        }*/

                /*->with(['amount' => $amount,
                        'email' => $email]);*/
    public function listBets(){
        if(Auth::check()) {
            $email = Auth::user()->email;
            $bets = Bet::where('game_id', 1)->take(10)->get();

//        $bets = Bet::all()->where('game_id', 1)->take(5);
        return view('colors')
            ->with(['bets'=> $bets, 'email'=>$email]);
        }
        else{return redirect('/');}
    }
}
/* Route::post('/task', function (Request $request) {
       $validator = Validator::make($request->all(), [
           'name' => 'required|max:255',
       ]);

       if ($validator->fails()) {
           return redirect('/')
               ->withInput()
               ->withErrors($validator);
       }

       $task = new Task;
       $task->name = $request->name;
       $task->save();

       return redirect('/');
   });*/