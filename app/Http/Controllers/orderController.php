<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;
use App\Models\order;
use Carbon\Carbon;
use App\Models\Device;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class orderController extends Controller
{
    public static function send($tokens, $title, $body)
    {
    $SERVER_API_KEY = 'AAAAoyYvGwU:APA91bGtPyjBO9fjrt-J9B5u72jRyJjiXCmDkZrHK-1QMGvUrqCElBBAtKuEczbcHhArpUS-xuaVtdAzRhUbRd0Atz1UFY1iVlC7i3ic0J1q_vpt-CkQDHIfptMrRAiRw-cC0pNEzzuY';
    $data = [
        "to" => $tokens,
        "notification" => [
            "title" => $title,
            "body" => $body,
            "sound"=> "default" // required for sound on ios
        ],
    ];
    $dataString = json_encode($data);
    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    $response = curl_exec($ch);
    //return $response;
    }

    public function createOrder(Request $request){
        $user=$request->user();
        if(!$user->tokenCan('User')){
            return response()->json([
                'message'=>__('msg.onlyUser')
            ]);
        }
        $carts=$request->all();
        $user_id=auth()->user()->id;
        $time=Carbon::now();
        $totalPrice=0;
        foreach($carts as $cart){
            $price=product::where('id',$cart['product_id'])->first();
            if($cart['amount']>$price['amount']){
                return response()->json([
                    'message'=>__('msg.dontHaveEnouphAmount')
                ]);
            }
            $totalPrice = ($price["price"]*$cart['amount']) + $totalPrice;
        }
        $order = order::create([
            'user_id'=>$user_id,
            'totalPrice'=>$totalPrice,
            'year'=>$time->year,
            'month'=>$time->month,
            'day'=>$time->day,
            'deliveryStatus'=>"in preparation",
            'paymentStatus'=>'Unpaid'
        ]);
        foreach($carts as $cart){
            DB::table('product_order')->insert([
                "order_id"=>$order['id'],
                'product_id'=>$cart['product_id'],
                'amount'=>$cart['amount']
            ]);
        }
        $username=$user->firstName;
        if($request->header('Lan_Token')=="ar"){
            $title="طلبية جديدة";
            $body="قام ".$username." بطلب طلبية جديد ";
        }else{
            $title="New_Order";
            $body=$username." sent new order";
        }
        $devices=Device::where('user_id',1)->get();
        foreach($devices as $device){
        $this->send($device->token_device,$title,$body);
        }
        Notification::create([
            'user_id'=>1,
            'title'=>$title,
            'message'=>$body
        ]);
        $username=$user->firstName;
        return response()->json([
            'message'=>__('msg.sentOrderforAdmin'),
            "Order"=>$order
        ],200);
    }
    public function showMyOrders(Request $request){
        $user=$request->user();
        if(!$user->tokenCan('User')){
            return response()->json([
                'message'=>__('msg.onlyUser')
            ]);
        }
        $user=auth()->user();
        if($user->myOrders->count()==0){
            return response()->json([
                'message'=>'you dont have any order'
            ]);
        }
        return response()->json([
            'Orders'=>$user->myOrders
        ]);
    }
    public function detailsForOrder($id){
        //$detailsForOrder=DB::table('product_order')->where('order_id',$id)->get();
        $detailsForOrder= DB::table('product_order as po')->where('po.order_id',$id)
        ->join('products','product_id','products.id')
        ->select('products.tradeName','products.price','po.amount','po.id')
        ->get();
        return response()->json([
            "detailsForOrder"=>$detailsForOrder
        ]);
    }
//////////////////////////////////////////////////////////////////////////////////////////////
    public function allOrders(Request $request){
        $user=$request->user();
        if(!$user->tokenCan('Admin')){
            return response()->json([
                'message'=>__('msg.onlyAdmin')
            ]);
        }
        $allOrders=order::with('user')->get();
        return response()->json([
            'Orders'=>$allOrders
        ]);
    }
    public function sendOrder(Request $request,$id){
        $user=$request->user();
        if(!$user->tokenCan('Admin')){
            return response()->json([
                'message'=>__('msg.onlyAdmin')
            ]);
        }
        $order = order::where('id',$id)->first();
        if($order['deliveryStatus']=='sent'){
            return response()->json([
                'message'=>__('msg.alreadysentOrder')
            ]);
        }
        order::where('id',$id)->update([
            'deliveryStatus'=>"sent"
        ]);
        $username=$user->firstName;
        if($request->header('Lan_Token')=="ar"){
            $title="ارسال الطلبية";
            $body=$username." المستودع ارسل الطلبية اليك ";
        }else{
            $title="Order_Sended";
            $body=$username." your order sended to you";
        }
        $devices=Device::where('user_id',$order->user_id)->get();
        foreach($devices as $device){
            $this->send($device->token_device,$title,$body);
        }
        Notification::create([
            'user_id'=>$order->user_id,
            'title'=>$title,
            'message'=>$body
        ]);
        $product_order=DB::table('product_order')->where('order_id',$order->id)->get();
        foreach($product_order as $m_o){
            $product=product::where('id',$m_o->product_id)->first();
            $newAmount=$product->amount - $m_o->amount;
            product::where('id',$m_o->product_id)->update([
                'amount'=>$newAmount
            ]);
        }
        $order = order::where('id',$id)->first();
        return response()->json([
            'meesage'=>__('msg.sendOrder'),
            'order'=>$order
        ]);
    }
    public function receiveMoney(Request $request,$id){
        $user=$request->user();
        if(!$user->tokenCan('Admin')){
            return response()->json([
                'message'=>__('msg.onlyAdmin')
            ]);
        }
        $order = order::where('id',$id)->first();
        if($order['paymentStatus']=='paid'){
            return response()->json([
                'message'=>__('msg.received_mony_already')
            ]);
        }
        order::where('id',$id)->update([
            'paymentStatus'=>'paid'
        ]);
        $username=$user->firstName;
        if($request->header('Lan_Token')=="ar"){
            $title="استلام النقود";
            $body=$username." تم استلام النقود من المستودع";
        }else{
            $title="Received_Money";
            $body=$username." the money received";
        }
        $order = order::where('id',$id)->first();
        $devices=Device::where('user_id',$order->user_id)->get();
        foreach($devices as $device){
            $this->send($device->token_device,$title,$body);
        }
        Notification::create([
            'user_id'=>$order->user_id,
            'title'=>$title,
            'message'=>$body
        ]);
        return response()->json([
            'meesage'=>__('msg.received_money'),
            'order'=>$order
        ]);
    }
    public function orderReceived(Request $request,$id){
        $user=$request->user();
        if(!$user->tokenCan('Admin')){
            return response()->json([
                'message'=>__('msg.onlyUser')
            ]);
        }
        $order = order::where('id',$id)->first();
        if($order['deliveryStatus']=='orderReceived'){
            return response()->json([
                'message'=>__('msg.already_received_order')
            ]);
        }
        order::where('id',$id)->update([
            'deliveryStatus'=>'orderReceived'
        ]);
        $username=$user->firstName;
        if($request->header('Lan_Token')=="ar"){
            $title="استلام الطلبية";
            $body=$username." لقد استلمت طلبيتك  ";
        }else {
            $title="Order_Received";
            $body=$username." you received your order";
        }
        $order = order::where('id',$id)->first();
        $devices=Device::where('user_id',$order->user_id)->get();
        foreach($devices as $device){
            $this->send($device->token_device,$title,$body);
        }
        Notification::create([
            'user_id'=>$order->user_id,
            'title'=>$title,
            'message'=>$body
        ]);
        return response()->json([
            'meesage'=>__('msg.received_order'),
            'order'=>$order
        ]);
    }
    public function deleteOrder(Request $request,$id){
        $user=$request->user();
        if(!$user->tokenCan('User')){
            return response()->json([
                'message'=>__('msg.onlyUser')
            ]);
        }
        $order=order::where('id',$id)->first();
        if($order->deliveryStatus=="sent"||$order->deliveryStatus=="orderReceived"){
            return response()->json([
                'message'=>__('msg.messageIfCantDeleteOrder')
            ]);
        }
        $username=$user->firstName;
        if($request->header('Lan_Token')=="ar"){
            $title="تم حذف الطلبية";
            $body=$username." قام بحذف طلبيته  ";
        }else {
            $title="Order_deleted";
            $body=$username." delete his order";
        }
        $devices=Device::where('user_id',1)->get();
        foreach($devices as $device){
            $this->send($device->token_device,$title,$body);
        }
        Notification::create([
            'user_id'=>1,
            'title'=>$title,
            'message'=>$body
        ]);
        $product_order=DB::table('product_order')->where('order_id',$order->id)->delete();
        order::where('id',$id)->delete();
        return response()->json([
            'message'=>__('msg.messageForDeleteOrder')
        ]);
    }
    public function allOrderHistory(Request $request){
        $user=$request->user();
        if(!$user->tokenCan('Admin')){
            return response()->json([
                'message'=>('msg.onlyAdmin')
            ]);
        }
        $order=order::whereBetween('created_at',[$request->startdate,$request->enddate])->get();
        if($order->count()==0){
            return response()->json([
                'message'=> ('msg.messageIfArntHaveOrderInHistory')
            ]);
        }
        return response()->json([
            'message'=> ('msg.messageIfHaveOrderInHistory'),
            'order'=>$order
        ]);
    }
    public function myOrderHistory(Request $request){
        $user=$request->user();
        if(!$user->tokenCan('User')){
            return response()->json([
                'message'=>('msg.onlyUser')
            ]);
        }
        $user=auth()->user()->id;
        $order=order::whereBetween('created_at',[$request->startdate,$request->enddate])->get();

        if($order->count()==0){
            return response()->json([
                'message'=> ('msg.messageIfArntHaveOrderInHistory')
            ]);
        }
        return response()->json([
            'message'=> ('msg.messageIfHaveOrderInHistory'),
            'order'=>$order
        ]);
    }
}
