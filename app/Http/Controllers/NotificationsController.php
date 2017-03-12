<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Notifications;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function seenNotification(Request $request){
        if(Auth::check()and $request->isMethod('post') and (int)$request->input('modal')==1 and !empty($request->input('id'))) {
            $id = (int)htmlspecialchars($request->input('id'),ENT_QUOTES);
            Notifications::seen($id);
        }
    }

    public function createNotification($params){

        $mustDeleted = array('lang','userId','text','textKey','type','titleKey','icon','translated');
        foreach($params as $k=>$v){
            if (in_array($k,$mustDeleted)){
                $params[$k] = htmlspecialchars($v,ENT_QUOTES);
                $key = array_search($k,$mustDeleted);
                unset($mustDeleted[$key]);
                if ($k=='textKey' and (int)$v==1) $params['text'] = Notifications::addTranslation($params['text'],$params['lang']);
                if ($k=='translated' and (int)$v==0) $params['titleKey'] = Notifications::addTranslation($params['titleKey'],$params['lang']);
            }
        }
        unset($params['translated']);
        if (count($mustDeleted)==0){
            Notifications::addNotification($params);
        }

    }
}