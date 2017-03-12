<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class Bet extends Model
{
    public $timestamps = false;
//    protected $table = 'bets';

    public static function getCurrentGames(){
        return DB::table('games')->where('isActive',1)->count();
    }

    public static function getMyActiveGames($userId){
        return DB::table('games')->leftJoin('bets', 'games.id', '=', 'bets.game_id')->where([['games.isActive','=',1],['bets.userId','=',$userId]])->count();
    }

    public static function getMyPlayedGames($userId){
        return DB::table('games')->leftJoin('bets', 'games.id', '=', 'bets.game_id')->where([['games.isActive','=',0],['bets.userId','=',$userId]])->groupBy('bets.game_id')->count();
    }
    public static function getMyWonGames($userId){
        return DB::table('games')->leftJoin('bets', 'games.id', '=', 'bets.game_id')->where([['games.isActive','=',0],['bets.userId','=',$userId],['bets.winner','=',1]])->groupBy('bets.game_id')->count();
    }

    public static function getStatistic($userId){
        $total = self::getMyPlayedGames($userId);
        $won = self::getMyWonGames($userId);
        if ($won>0) return (100*($total/$won)).'%';
        else return '0%';
    }
    public static function getPlaceInTop($userId){
        return DB::table('users')->select('top')->where('id','=',$userId)->first()->top;
    }
    public static function getMyBalance($userId){
        return DB::table('users')->select('balance')->where('id','=',$userId)->first()->balance;
    }

    public static function unsetFinishedGames(){
        $time = time()-(20*60);
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
}
