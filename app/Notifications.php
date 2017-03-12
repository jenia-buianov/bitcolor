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

    public static function seen($id){
        return DB::table('notifications')->where([['notificationId','=',$id]])->update(['seen'=>1]);
    }

    public static function unseenNotifications($userId){
        return DB::table('notifications')->select('*')->where([['userId','=',$userId],['seen','=',0],['shown','=',0]])->get();
    }

    public static function setShown($id){
        return DB::table('notifications')->where([['notificationId','=',$id]])->update(['shown'=>1]);
    }

    public static function addTranslation($text,$lang){
        $key = 'notification_'.time();
        DB::table('translations')->insert(['lang'=>$lang,'text'=>$text,'key'=>$key]);
        return $key;
    }

    public static function addNotification($array){
        DB::table('notifications')->insert($array);
    }


}
