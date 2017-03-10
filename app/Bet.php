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
}
