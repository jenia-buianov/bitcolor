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
}