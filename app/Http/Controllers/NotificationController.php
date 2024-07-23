<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function Notification(Request $request){
        $user_id = $request->user()->id;
        $notification = Notification::where('user_id',$user_id)->get();
        return response()->json([
            'Notification'=>$notification
        ]);
    }
}
