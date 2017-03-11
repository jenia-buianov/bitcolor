<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class Notifications extends Model
{
    public $timestamps = false;
//    protected $table = 'bets';

    public static function countNotifications($userId){
        return DB::table('notifications')->where([['seen','=',0],['userId','=',$userId]])->count();
    }

    public static function lastNotifications($userId){
        return DB::table('notifications')->select('*')->where([['userId','=',$userId]])->get();
    }
}
