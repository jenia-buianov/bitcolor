<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Bet;
use App\Notifications;
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
            Bet::unsetFinishedGames();
        return view('game.colors')
            ->with(['listNotif'=>Notifications::lastNotifications(Auth::user()->id),
                'countNotif'=>Notifications::countNotifications(Auth::user()->id),
                'timePerGame'=>(20*60),'games'=>Bet::listMyActiveGames(Auth::user()->id),
                'balance'=>Bet::getMyBalance(Auth::user()->id),
                'currGames'=> Bet::getCurrentGames(),
                'myActive'=> Bet::getMyActiveGames(Auth::user()->id),
                'myStatistic'=>Bet::getStatistic(Auth::user()->id),
                'myPlayedGames'=>Bet::getMyPlayedGames(Auth::user()->id),
                'top'=>Bet::getPlaceInTop(Auth::user()->id)]);
        }
        else{return redirect('/');}
    }

    public function postActiveGames(Request $request){
        if(Auth::check() and $request->isMethod('post') and (int)$request->input('modal')==1) {
            Bet::unsetFinishedGames();
            return view('game.game')->with(['games'=>Bet::listMyActiveGames(Auth::user()->id),'timePerGame'=>(20*60)]);
        }
    }

    public function getActiveGames(){
        if(Auth::check()) {
            Bet::unsetFinishedGames();
            return view('game.colors')
                ->with(['listNotif'=>Notifications::lastNotifications(Auth::user()->id),
                    'countNotif'=>Notifications::countNotifications(Auth::user()->id),
                    'timePerGame'=>(20*60),'games'=>Bet::listMyActiveGames(Auth::user()->id),
                    'balance'=>Bet::getMyBalance(Auth::user()->id),
                    'currGames'=> Bet::getCurrentGames(),
                    'myActive'=> Bet::getMyActiveGames(Auth::user()->id),
                    'myStatistic'=>Bet::getStatistic(Auth::user()->id),
                    'myPlayedGames'=>Bet::getMyPlayedGames(Auth::user()->id),
                    'top'=>Bet::getPlaceInTop(Auth::user()->id)]);
        }
        else{return redirect('/');}
    }

    public function listGames(Request $request){
        if(Auth::check() and $request->isMethod('post') and (int)$request->input('modal')==1) {
            Bet::unsetFinishedGames();
            return view('game.game')->with(['games'=>Bet::listAllGames(),'timePerGame'=>(20*60)]);
        }
    }

    private function notificationObserver(){
        $text = "";
        foreach (Notifications::lastNotifications(Auth::user()->id) as $k=>$v){
            $text.='<li><a href="';
            if ($v->modal==1) $text.=$v->link; else $text.='#';
            $text.='">';
            if ($v->textKey==1) $text.=translate($v->text);
            elseif($v->lang==lang()) $text.=$v->text;
            $text.="</a></li>\n";
        }
        return $text;
    }

    public function observer(Request $request){
        if(Auth::check()and $request->isMethod('post') and (int)$request->input('modal')==1) {
            $response['currGames'] = array('type'=>'#','value'=>Bet::getCurrentGames(),'action'=>'set','effect'=>'','equal'=>false);
            $response['myActive'] = array('type'=>'#','value'=>Bet::getMyActiveGames(Auth::user()->id),'action'=>'set','effect'=>'','equal'=>false);
            $response['myStatistic'] = array('type'=>'#','value'=>Bet::getStatistic(Auth::user()->id),'action'=>'set','effect'=>'','equal'=>false);
            $response['myPlayedGames'] = array('type'=>'#','value'=>Bet::getMyPlayedGames(Auth::user()->id),'action'=>'set','effect'=>'','equal'=>false);
            $response['placeTop'] = array('type'=>'#','value'=>Bet::getPlaceInTop(Auth::user()->id),'action'=>'set','effect'=>'','equal'=>false);
            $response['my_balance'] = array('type'=>'#','value'=>Bet::getMyBalance(Auth::user()->id).' <i class="fa fa-btc" aria-hidden="true" style="color:#FF9800"></i>','action'=>'set','effect'=>'bounceIn','equal'=>true);
            $countNotif = Notifications::countNotifications(Auth::user()->id);
            if ($countNotif>0) $css = array('display'=>'-webkit-inline-box'); else $css = array('display'=>'none');
            $response['notif_count'] = array('type'=>'.','value'=>$countNotif,'action'=>'set','effect'=>'','equal'=>false,'css'=>$css);
            $response['notif_top'] = array('type'=>'.','value'=>$this->notificationObserver(),'action'=>'set','effect'=>'','equal'=>false);

            //$response['notif_count'] = array('type'=>'.','value'=>1,'action'=>'set','effect'=>'','equal'=>false,'css'=>array('display'=>'-webkit-box'));
           // $response['not'] = array('type'=>'notification','notif'=>array('type'=>'warning','text'=>'HERE is Text','title'=>'title','icon'=>'btc'));
            echo json_encode($response);
            exit;
        }
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