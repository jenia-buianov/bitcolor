<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class Bet extends Model
{
    public $timestamps = false;
//    protected $table = 'bets';

    public function __construct()
    {
        //parent::__construct($attributes);
        DB::enableQueryLog();
    }

    public static function getCurrentGames(){
        return DB::table('games')->where('isActive',1)->count();
    }

    public static function getMyActiveGames($userId){
        return DB::table('bets')->select(DB::raw("COUNT(DISTINCT `bets`.`game_id`) as total"))->leftJoin('games', 'games.id', '=', 'bets.game_id')->where([['games.isActive','=',1],['bets.userId','=',$userId]])->first()->total;

    }

    public static function getMyPlayedGames($userId){
        return DB::table('games')->select(DB::raw('DISTINCT bets.game_id'))->leftJoin('bets', 'games.id', '=', 'bets.game_id')->where([['games.isActive','=',0],['bets.userId','=',$userId]])->count();
    }
    public static function getMyWonGames($userId){
        //DB::enableQueryLog();
        return DB::table('games')->select(DB::raw('DISTINCT bets.game_id'))->leftJoin('bets', 'games.id', '=', 'bets.game_id')->where([['games.isActive','=',0],['bets.userId','=',$userId],['bets.winner','=',1]])->count();
        //print_r(DB::getQueryLog());
        //exit;
    }

    public static function getStatistic($userId){
        $total = self::getMyPlayedGames($userId);
        $won = self::getMyWonGames($userId);
        if ($won>0) return number_format((100*($won/$total)),2,'.','').'%';
        else return '0%';
    }
    public static function getPlaceInTop($userId){
        return DB::table('users')->select('top')->where('id','=',$userId)->first()->top;
    }
    public static function getMyBalance($userId){
        return DB::table('users')->select('balance')->where('id','=',$userId)->first()->balance;
    }

    public static function unsetFinishedGames($time){
        $time = time()-($time*60);
        DB::table('games')->where('timeStart','<', $time+1)
            ->update(['isActive' => 0,'finished_at'=>date('Y-m-d H:m:s')]);
    }

    public static function listAllGames(){
        return DB::table('games')->select("id", "timeStart as time", DB::raw("(SELECT COUNT(DISTINCT bets.userId) FROM `bets` WHERE bets.game_id=games.id) as players"), DB::raw("(SELECT SUM(bets.amount) FROM `bets` WHERE bets.game_id=games.id) as money"))->where([['isActive','=',1]])->get();
    }

    public static function listMyActiveGames($userId){
        return DB::table('games')->select("games.id", "timeStart as time", DB::raw("(SELECT COUNT(DISTINCT bets.userId) FROM `bets` WHERE bets.game_id=games.id) as players"), DB::raw("(SELECT SUM(bets.amount) FROM `bets` WHERE bets.game_id=games.id) as money"))->leftJoin('bets', 'games.id', '=', 'bets.game_id')->where([['games.isActive','=',1],['bets.userId','=',$userId]])->groupBy('bets.game_id')->get();
    }

    public static function createGame($array){
        DB::table('games')->insert($array);
    }

    public static function loadGameInfo($id,$userId){
        //DB::enableQueryLog();
        return DB::table('games')->select(DB::raw("(SELECT COUNT(DISTINCT bets.userId) FROM `bets` WHERE bets.game_id=games.id) as players"), 'timeStart as time', 'win_sector', 'isActive', 'started_at', 'finished_at', 'zipfile', 'zipPassword','games.id',DB::raw("(SELECT `sector` FROM `bets` WHERE `bets`.`game_id`='".$id."' AND `bets`.`userId`='".$userId."' LIMIT 1) as color"),DB::raw("(SELECT SUM(`amount`) FROM `bets` WHERE `bets`.`game_id`=`games`.`id` AND `bets`.`userId`='".$userId."') as money"), DB::raw("(SELECT SUM(bets.amount) FROM `bets` WHERE bets.game_id=games.id) as bank"))->where('games.id','=',$id)->get();
        //print_r(DB::getQueryLog());
        //exit;
    }

    public static function loadBets($id){
        return DB::table('bets')->select('sector',DB::raw("SUM(`amount`) as bank"))->where('game_id','=',$id)->groupBy('sector')->get();
    }

    public static function addBet($array){
        DB::table('bets')->insert($array);
    }

    public static function setMoney($amount,$userId){
        DB::table('users')->where([['id','=',$userId]])->update(['balance'=>$amount]);
    }

    public static function loadMoneyWithoutSector($sector,$id){
        return  DB::table('bets')->select(DB::raw('SUM(amount) as total'))->where([['game_id','=',$id],['sector','!=',$sector]])->first();
    }

    public static function loadUnsetGamesId($time){
        $time = time()-($time*60);
        return DB::table('games')->select('id')->where([['isActive','=',1],['timeStart','<',$time]])->get();
    }

    public static function getBankGame($id){
        return DB::table('bets')->select(DB::raw("SUM(amount) as total"))->where('game_id','=',$id)->first();
    }

    public static function getWinnerSector($id){
        return DB::table('games')->select('win_sector')->where('id','=',$id)->first()->win_sector;
    }

    public static function getWinnersId($id){
        return DB::table('bets as b')->select(DB::raw('DISTINCT userId'), DB::raw("(SELECT SUM(`amount`) FROM `bets` as b2 WHERE `b`.`userId`=`b2`.`userId` AND `b2`.`game_id` = ".$id.")as money"))->where([['b.game_id','=',$id],['b.winner','=',1]])->get();
    }

    public static function getLosersId($id){
        return DB::table('bets as b')->select(DB::raw('DISTINCT b.userId'), DB::raw("(SELECT SUM(`amount`) FROM `bets` as b2 WHERE `b`.`userId`=`b2`.`userId` AND `b2`.`game_id` = ".$id.")as money"))->where([['b.game_id','=',$id],['b.winner','=',0]])->get();
    }

    public static function setWinners($id,$sector){
        DB::table('bets')->where('game_id','=',$id)->where('sector','=',$sector)->update(['winner'=>1]);
    }

    public static function getWinnerBank($id){
        return DB::table('bets')->select(DB::raw("SUM(amount) as total"))->where([['game_id','=',$id],['winner','=','1']])->first()->total;
    }

    public static function getUserLang($userId){
        return DB::table('users')->select('lang')->where('id','=',$userId)->first()->lang;
    }

    public static function getPlayers($id,$color){
        return DB::table('bets')->select(DB::raw("DISTINCT `userId`"), DB::raw("COUNT(DISTINCT `userId`) as count"), DB::raw("(SELECT SUM(`amount`) FROM `bets` as `b` WHERE `b`.`game_id`='".$id."' AND `b`.`sector`!='".$color."') as mcolors"))->where('game_id','=',$id)->first();
    }

    public static function getMoneyInGame($game,$user){
        return DB::table('bets')->select(DB::raw("(SELECT SUM(`amount`) FROM `bets` WHERE `game_id`='".$game."') as money"))->where('game_id','=',$game)->first()->money;
    }

    public static function addTransfer($array){
        DB::table('money_transfers')->insert($array);
    }

    public static function seUserTop($userId,$place){
        DB::table('users')->where('id','=',$userId)->update(['top'=>$place]);
    }

    public static function setSuccess($success,$userId){
        DB::table('users')->where('id','=',$userId)->update(['success'=>$success]);
    }
    public static function getAllUsersByStatistic(){
        return DB::table('users')->select('id as userId')->orderBy('success','desc')->get();
    }

    public static function loadUnsetGames($time){
        $time = time()-($time*60);
        return DB::table('games')->select('games.id','games.win_sector', DB::raw("(SELECT SUM(`bets`.`amount`) FROM `bets` WHERE `games`.`id`=`bets`.`game_id`) as `bank`"))->where([['games.isActive','=',1],['games.timeStart','<',$time]])->get();
    }

}
