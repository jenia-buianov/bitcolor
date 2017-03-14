<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use ZipArchive;
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

    public function bet(Request $request){
        if(Auth::check() and $request->isMethod('post')) {
            $page = explode('/',trim($request->input('page')));
            $id = $page[3];
            $date = array('html'=>'You have started playing this game', 'error'=>'');
            $data = makeData(array('color','amount'),array('color'=>'sector'),$request->input('values'),$date,array('game_id'=>$id,'userId'=>Auth::user()->id));
            if (!empty($date['error']))
            {
                echo json_encode($date);
                retrun;
            }
            else{
                $balance = Bet::getMyBalance(Auth::user()->id);
                if ($data['amount']>$balance) $date['error'] = "Don't have enough money";

                $info = Bet::loadGameInfo($id,Auth::user()->id);
                $otherColors = Bet::loadMoneyWithoutSector($data['sector'],$id);
                if ($info[0]->isActive==0) $date['error'] = "Game have finished yet";
                if(!empty($info[0]->color)and $data['sector']!==$info[0]->color) $data['error'] = "You cannot change color";

                if($info[0]->bank > 0 and $otherColors->total < ($data['amount'] + $info[0]->money)) $date['error'] = "You cannot put such a big amount";

                if (!empty($date['error']))
                {
                    echo json_encode($date);
                    return ;
                }
                Bet::setMoney($balance - $data['amount'],Auth::user()->id);
                Bet::addBet($data);
            }
            echo json_encode($date);
            return;
        }
    }

    private function getWithArray($type = null){
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

    private function postWithArray($type = null){
        if(empty($this->postWithArray)) {
            $this->postWithArray = array('timePerGame'=>($this->timePerGame*60));
            if ($type=='my') $this->postWithArray['games'] =  Bet::listMyActiveGames(Auth::user()->id);
            if ($type=='all') $this->postWithArray['games'] =  Bet::listAllGames(Auth::user()->id);
        }
        return $this->postWithArray;
    }

    public function listBets(){
        if(Auth::check()) {
            return view('game.colors')
                ->with($this->getWithArray('all'));
        }
        else{return redirect('/');}
    }


    public function listGames(Request $request){
        if(Auth::check() and $request->isMethod('post') and (int)$request->input('modal')==1) {
            return view('game.game')->with($this->postWithArray('all'));
        }
    }

    public function postActiveGames(Request $request){
        if(Auth::check() and $request->isMethod('post') and (int)$request->input('modal')==1) {
            return view('game.game')->with($this->postWithArray('my'));
        }
    }

    public function getActiveGames(){
        if(Auth::check()) {
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

    public function unsetFinishedGames(){

        //if ($_SERVER['REMOTE_ADDR']!=='31.22.4.254' and  $_SERVER['REMOTE_ADDR']!=='31.22.4.41') return;
        $unset = Bet::loadUnsetGamesId($this->timePerGame);
        Bet::unsetFinishedGames($this->timePerGame);
        foreach($unset as $k=>$v){
            $bank = Bet::getBankGame($v->id);
            if (count($bank)==1) {
                $bank = $bank->total;
                //print_r($bank);
                if(!empty($bank)) {
                    $winSector = Bet::getWinnerSector($v->id);
                    $players = Bet::getPlayers($v->id);
                    Bet::setWinners($v->id,$winSector);
                    $winners = Bet::getWinnersId($v->id);
                    $losers = Bet::getLosersId($v->id);
                    $otherColors = Bet::loadMoneyWithoutSector($winSector,$v->id);
                    if($players>1 and count($otherColors)>0 and isset($otherColors->total) and !empty($otherColors->total))
                    {
                        $winnerBank = Bet::getWinnerBank($v->id);
                        $bank*=0.98;

                        if(count($winners)>0 and !empty($winners[0]->userId)){
                            foreach ($winners as $item=>$value){
                                $giveMoney = number_format(($bank/$winnerBank)*$value->money, 8, '.', '');
                                $setMoney = Bet::getMyBalance($value->userId)+$giveMoney;
                                Bet::setMoney($setMoney,$value->userId);
                                $create = Notif::createNotification(array('type'=>'success','lang'=>Bet::getUserLang($value->userId),'userId'=>$value->userId,'text'=> 'Congratulations','textKey'=>1,'titleKey'=>'Won Game #'.$v->id.' +'.$giveMoney,'icon'=>'trophy','translated'=>0));
                                print_r($create.' '.$v->id.' '.$value->userId);
                                if(!is_bool($create)){
                                    print_r($create);
                                }
                            }
                        }
                        if (count($losers)>0 and !empty($losers[0]->userId)) {
                            foreach ($losers as $item => $value) {
                                $create = Notif::createNotification(array('type' => 'warning', 'lang' => Bet::getUserLang($value->userId), 'userId' => $value->userId, 'text' => "Don't worry. Try again", 'textKey' => 1, 'titleKey' => 'You lose Game #' . $v->id, 'icon' => 'trophy', 'translated' => 0));
                                print_r($create . ' ' . $v->id . ' ' . $value->userId);
                                if (!is_bool($create)) {
                                    print_r($create);
                                }
                            }
                        }

                    }else{
                        if(count($winners)>0 and !empty($winners[0]->userId))
                            foreach ($winners as $item=>$value){
                                Bet::setMoney($value->money,$value->userId);
                                $create = Notif::createNotification(array('type'=>'info','lang'=>Bet::getUserLang($value->userId),'userId'=>$value->userId,'text'=> 'Nobody won','textKey'=>1,'titleKey'=>'Equal Game #'.$v->id,'icon'=>'trophy','translated'=>0));
                                print_r($create.' '.$v->id.' '.$value->userId);
                                if(!is_bool($create)){
                                    print_r($create);
                                }
                            }
                        if(count($losers)>0 and !empty($losers[0]->userId))
                            foreach ($losers as $item => $value) {
                                $create = Notif::createNotification(array('type' => 'info', 'lang' => Bet::getUserLang($value->userId), 'userId' => $value->userId, 'text' => 'Nobody won', 'textKey' => 1, 'titleKey' => 'Equal Game #' . $v->id, 'icon' => 'trophy', 'translated' => 0));
                                print_r($create . ' ' . $v->id . ' ' . $value->userId);
                                if (!is_bool($create)) {
                                    print_r($create);
                                }
                            }
                    }

                }
            }
        }

    }

    public function observer(Request $request){
        if(Auth::check()and $request->isMethod('post') and (int)$request->input('modal')==1) {
            //$this->unsetFinishedGames();
            $response['currGames'] = array('type'=>'#','value'=>Bet::getCurrentGames(),'action'=>array('a1'=>'set'),'effect'=>'','equal'=>false);
            $response['myActive'] = array('type'=>'#','value'=>Bet::getMyActiveGames(Auth::user()->id),'action'=>array('a1'=>'set'),'effect'=>'','equal'=>false);
            $response['myStatistic'] = array('type'=>'#','value'=>Bet::getStatistic(Auth::user()->id),'action'=>array('a1'=>'set'),'effect'=>'','equal'=>false);
            $response['myPlayedGames'] = array('type'=>'#','value'=>Bet::getMyPlayedGames(Auth::user()->id),'action'=>array('a1'=>'set'),'effect'=>'','equal'=>false);
            $response['placeTop'] = array('type'=>'#','value'=>Bet::getPlaceInTop(Auth::user()->id),'action'=>array('a1'=>'set'),'effect'=>'','equal'=>false);
            $response['my_balance'] = array('type'=>'#','value'=>Bet::getMyBalance(Auth::user()->id).' <i class="fa fa-btc" aria-hidden="true" style="color:#FF9800"></i>','action'=>array('a1'=>'set'),'effect'=>'bounceIn','equal'=>true);
            $countNotif = Notifications::countNotifications(Auth::user()->id);
            if ($countNotif>0) $css = array('display'=>'-webkit-inline-box'); else $css = array('display'=>'none');
            $response['notif_count'] = array('type'=>'.','value'=>$countNotif,'action'=>array('a1'=>'set'),'effect'=>'','equal'=>false,'css'=>$css);
            $response['notif_top'] = array('type'=>'.','value'=>$this->notificationObserver(),'action'=>array('a1'=>'set'),'effect'=>'','equal'=>false);

            foreach($this->notifier() as $k=>$v) {
                $response[$k] = $v;
            }
            $page = explode('/',trim($request->input('page')));
            if (isset($page[2]) and $page[2]=='view' and isset($page[3])&&!empty($page[3]))
            {
                $id = (int)$page[3];
                $info = Bet::loadGameInfo($id,Auth::user()->id);
                if ($info[0]->isActive==1) {
                    if ($info[0]->bank == 0) $info[0]->bank = 0;
                    $response['bank'] = array('type' => '#', 'value' => $info[0]->bank . ' <i class="fa fa-btc" aria-hidden="true" style="color:#FF9800"></i>', 'action' => array('a1' => 'set'), 'effect' => 'bounceIn', 'equal' => true);
                    $response['players'] = array('type' => '#', 'value' => 'Players: ' . $info[0]->players . ' <i class="fa fa-users" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i>', 'action' => array('a1' => 'set'), 'effect' => '', 'equal' => true);
                    $sectors = array('lucky', 'violet', 'orange', 'red', 'yellow', 'green', 'cyan', 'blue');

                    foreach (Bet::loadBets($id) as $k => $v) {
                        $response[$v->sector] = array('type' => '#', 'value' => '<div>' . $v->bank . ' <i class="fa fa-btc" aria-hidden="true" style="color:#FF9800"></i></div>', 'action' => array('a1' => 'set'), 'effect' => '', 'equal' => true);
                        unset($sectors[array_search($v->sector, $sectors)]);
                    }
                    if (count($sectors) > 0)
                        foreach ($sectors as $i => $v) {
                            $response[$v] = array('type' => '#', 'value' => '<div>0 <i class="fa fa-btc" aria-hidden="true" style="color:#FF9800"></i></div>', 'action' => array('a1' => 'set'), 'effect' => '', 'equal' => true);
                        }
                    if ($info[0]->money > 0) {
                        $sectors = array('lucky', 'violet', 'orange', 'red', 'yellow', 'green', 'cyan', 'blue');
                        for ($i = 0; $i < count($sectors); $i++)
                            $response[$sectors[$i]]['action']['a2'] = 'no_click';
                        $response['my_amount'] = array('type' => '#', 'value' => 'My amount: ' . $info[0]->money . ' <i class="fa fa-btc" aria-hidden="true" style="color:#FF9800"></i>', 'action' => array('a1' => 'set'), 'effect' => 'bounceIn', 'equal' => true, 'css' => array('display' => 'block'));
                        $response['my_color'] = array('type' => '#', 'value' => 'Selected color: ' . $info[0]->color, 'action' => array('a1' => 'set'), 'effect' => 'bounceIn', 'equal' => true, 'css' => array('display' => 'block'));

                    }
                }else{
                    $value='<div class="bank"><span id="bank">';
                    if($info[0]->bank>0)$value.=$info[0]->bank; else $value.='0';
                    $value.='<i class="fa fa-btc" aria-hidden="true" style="color:#ff9800;"></i></span>    </div>';
                    $value.='<div id="players" style="font-size:1em;display:block;width:100%;text-align:center">Players: '.(int)$info[0]->players.' <i class="fa fa-users" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i></div>
                            <div style="text-align: center">Game finished: '.mb_substr($info[0]->finished_at,0,16).'</div>
                            <div style="text-align: center">Win color: '.$info[0]->win_sector.'</div>';
                    if($info[0]->money > 0)$value.='<div style="text-align: center">Zip password: '.$info[0]->zipPassword.'</div>';
                    $response['info'] = array('where'=>'#mainContent', 'type'=>'#','value'=>$value,'action'=>array('a1'=>'updIsset'),'effect'=>'bounceIn','equal'=>true);
                }

            }
            echo json_encode($response);
            exit;
        }
    }

    public function createGame(){
        if ($_SERVER['REMOTE_ADDR']!=='31.22.4.254' and  $_SERVER['REMOTE_ADDR']!=='31.22.4.41') return;
        $sectors = 8;
        $selectedWinner = '';
        $zipFile = '';
        $zipPassword = '';
        $time = time();
        $winnersArray = array();
        $winnersTitles = array('lucky','violet','orange','red','yellow','green','cyan','blue');

        for($k=0;$k<rand(0,250);$k++){
            $winnersArray[$k] = $winnersTitles[rand(0,$sectors-1)];
        }
        $selectedWinner = $winnersArray[rand(0,count($winnersArray)-1)];
        $zipPassword = md5(md5($time).$selectedWinner.rand(0,1000));
        $zipPassword = substr($zipPassword,0,100);
        $zipFile = 'game_'.$time;
        $fp = fopen($zipFile.'.txt', "w");
        fwrite($fp, "Game created: ".date('d.m.Y H:i:s').",  Winner block: ".$selectedWinner);
        fclose($fp);
        $zip = new ZipArchive();
        $zip->open($zipFile.'.zip', ZIPARCHIVE::CREATE);
        $zip->setPassword($zipPassword);
        $zip->close();
        system('zip -P '.$zipPassword.' '.$zipFile.'.zip -j '.$zipFile.'.txt');
        copy($zipFile.'.zip',dirname(__FILE__).'/../../../public/results/'.$zipFile.'.zip');
        unlink($zipFile.'.txt');
        unlink($zipFile.'.zip');
        $insertArray = array('win_sector'=>$selectedWinner,'zipfile'=>$zipFile,'zipPassword'=>$zipPassword,'timeStart'=>$time);
        Bet::createGame($insertArray);
    }

    public function getViewGame(Request $request){
        if(Auth::check()) {
            $id = $request->id;
            $array = $this->getWithArray();
            foreach(Bet::loadGameInfo($id,Auth::user()->id)[0] as $k=>$v){
                $array['game'][$k] = $v;
            }

            $array['bets'] = Bet::loadBets($id);
            $array['sectors'] = array('lucky','violet','orange','red','yellow','green','cyan','blue');
            return view('game.view')
                ->with($array);
        }
        else{return redirect('/');}

    }

    public function postViewGame(Request $request){
        if(Auth::check()) {
            $id = $request->id;
            $array = $this->postWithArray();
            foreach(Bet::loadGameInfo($id,Auth::user()->id)[0] as $k=>$v){
                $array['game'][$k] = $v;
            }

            $array['bets'] = Bet::loadBets($id);
            $array['sectors'] = array('lucky','violet','orange','red','yellow','green','cyan','blue');
            return view('game.content')
                ->with($array);
        }
        else{return redirect('/');}
    }

}
