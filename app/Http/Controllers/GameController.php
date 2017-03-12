<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Bet;
use App\Notifications;
use App\Http\Controllers\NotificationsController as Notif;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    private $timePerGame = 20;
    private $getWithArray = array();
    private $postWithArray = array();

    private function getWithArray($type){
        if (empty($this->getWithArray)) {
            $this->getWithArray = array(
                'listNotif' => Notifications::lastNotifications(Auth::user()->id),
                'countNotif' => Notifications::countNotifications(Auth::user()->id),
                'timePerGame' => ($this->timePerGame * 60),
                'balance' => Bet::getMyBalance(Auth::user()->id),
                'currGames' => Bet::getCurrentGames(),
                'myActive' => Bet::getMyActiveGames(Auth::user()->id),
                'myStatistic' => Bet::getStatistic(Auth::user()->id),
                'myPlayedGames' => Bet::getMyPlayedGames(Auth::user()->id),
                'top' => Bet::getPlaceInTop(Auth::user()->id));
            if ($type=='my') $this->getWithArray['games'] =  Bet::listMyActiveGames(Auth::user()->id);
            if ($type=='all') $this->getWithArray['games'] =  Bet::listAllGames(Auth::user()->id);

        }
        return $this->getWithArray;
    }

    private function postWithArray($type){
        if(empty($this->postWithArray)) {
            $this->postWithArray = array('timePerGame'=>($this->timePerGame*60));
            if ($type=='my') $this->postWithArray['games'] =  Bet::listMyActiveGames(Auth::user()->id);
            if ($type=='all') $this->postWithArray['games'] =  Bet::listAllGames(Auth::user()->id);
        }
        return $this->postWithArray;
    }

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
            ->with($this->getWithArray('all'));
        }
        else{return redirect('/');}
    }


    public function listGames(Request $request){
        if(Auth::check() and $request->isMethod('post') and (int)$request->input('modal')==1) {
            Bet::unsetFinishedGames();
            return view('game.game')->with($this->postWithArray('all'));
        }
    }

    public function postActiveGames(Request $request){
        if(Auth::check() and $request->isMethod('post') and (int)$request->input('modal')==1) {
            Bet::unsetFinishedGames();
            return view('game.game')->with($this->postWithArray('my'));
        }
    }

    public function getActiveGames(){
        if(Auth::check()) {
            Bet::unsetFinishedGames();
            return view('game.colors')
                ->with($this->getWithArray('my'));
        }
        else{return redirect('/');}
    }

    private function notificationObserver(){
        $text = "";
        foreach (Notifications::lastNotifications(Auth::user()->id) as $k=>$v){
            $text.='<li';
            if($v->seen==0) $text.=' class="unseen" onmouseover="notificationSeen('.$v->notificationId.')"';
            $text.='><a href="';
            if ($v->modal==1) $text.=$v->link; else $text.='#';
            $text.='">';$text.=translate($v->titleKey);
            $text.="</a></li>\n";
        }
        return $text;
    }

    private function notifier(){
        $array = array();
        foreach (Notifications::unseenNotifications(Auth::user()->id) as $k=>$v){
            if ($v->textKey==1) $text = translate($v->text); else $text = $v->text;
            $array['not'.$v->notificationId] = array('type'=>'notification','notif'=>array('type'=>$v->type,'text'=>$text,'title'=>translate($v->titleKey),'icon'=>$v->icon));
            Notifications::setShown($v->notificationId);
        }
        return $array;
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

            foreach($this->notifier() as $k=>$v) {
                $response[$k] = $v;
            }
            // $response['not'] = array('type'=>'notification','notif'=>array('type'=>'warning','text'=>'HERE is Text','title'=>'title','icon'=>'btc'));
            echo json_encode($response);
            exit;
        }
    }

    public function createGame(){
        //if ($_SERVER['REMOTE_ADDR']!=='127.0.0.1') return;
        $sectors = 8;
        $selectedWinner = '';
        $zipFile = '';
        $zipPassword = '';
        $winnersArray = array();
        $winnersTitles = array('lucky','violet','orange','red','yellow','green','cyan','blue');

        for($k=0;$k<rand(0,250);$k++){
            $winnersArray[$k] = $winnersTitles[rand(0,$sectors-1)];
        }
        $selectedWinner = $winnersArray[rand(0,count($winnersArray)-1)];
        $zipPassword = md5(md5(time()).$selectedWinner.rand(0,1000));
        $zipPassword = substr($zipPassword,0,100);
        $zipFile = 'game_'.time();
        $fp = fopen(dirname(__FILE__).'/../../../public/results/'.$zipFile.'.txt', "w");
        fwrite($fp, "Winner block: ".$selectedWinner);
        fclose($fp);
        $zip = new ZipArchive();
        $zip->open(dirname(__FILE__).'/../../../public/results/'.$zipFile.'.zip', ZIPARCHIVE::CREATE);
        $zip->addFile(dirname(__FILE__).'/../../../public/results/'.$zipFile.'.txt');
        $zip->setPassword($zipPassword);
        $zip->close();
        unlink(dirname(__FILE__).'/../../../public/results/'.$zipFile.'.txt');
        $insertArray = array('win_sector'=>$selectedWinner,'zipfile'=>$zipFile,'zipPassword'=>$zipPassword);
        Bet::createGame();

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