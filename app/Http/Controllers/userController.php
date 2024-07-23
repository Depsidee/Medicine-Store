<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class userController extends Controller
{
    //
    public function registerToAdmin(Request $request){
        $validator=validator::make($request->all(),[
            "firstName"=>'required|min:2|max:10',
            "lastName"=>'required|min:2|max:10',
            "phoneNumber"=>'required|min:10|max:10|unique:users',
            "password"=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                "message"=>"Validation fails",
                "errors"=>$validator->errors()
            ],400);
        }
        $user= User::create([
            "firstName"=>$request->firstName,
            "lastName"=>$request->lastName,
            "phoneNumber"=>$request->phoneNumber,
            "password"=>Hash::make($request->password),
            'role'=>'Admin'
        ]);
        $device=Device::create([
            'user_id'=>$user->id,
            'token_device'=>$request->tokenDevice
        ]);
        $accessPoint=['Admin'];
        $token=$user->createToken('token',$accessPoint)->plainTextToken;
        return response()->json([
            "message"=>"register is true",
            "token"=>$token,
            "user"=>$user
        ],200);
    }
    public function registerToUser(Request $request){
        $validator=validator::make($request->all(),[
            "firstName"=>'required|min:2|max:10',
            "lastName"=>'required|min:2|max:10',
            "phoneNumber"=>'required|min:10|max:10|unique:users',
            "password"=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                "message"=>"Validation fails",
                "errors"=>$validator->errors()
            ],400);
        }
        $user= User::create([
            "firstName"=>$request->firstName,
            "lastName"=>$request->lastName,
            "phoneNumber"=>$request->phoneNumber,
            "password"=>Hash::make($request->password),
            'role'=>'User'
        ]);
        $device=Device::create([
            'user_id'=>$user->id,
            'token_device'=>$request->tokenDevice
        ]);
        $accessPoint=['User'];
        $token=$user->createToken('token',$accessPoint)->plainTextToken;
        return response()->json([
            "message"=>"register is true",
            "token"=>$token,
            "user"=>$user
        ],200);
    }
    public function login(Request $request) {
        $validator=validator::make($request->all(),[
            'phoneNumber'=>'required',
            'password'=>'required'
        ]);
        if($validator->fails()){
            return response()->json([
                "message"=>"Validation fails",
                "errors"=>$validator->errors()
            ],400);
        }
        $user=User::where('phoneNumber',$request->phoneNumber)->first();
        if(!$user) {
        return response()->json([
            'message'=>'Wrong phone number'
        ]);
        }
        if(!(Hash::check($request->password,$user->password))) {
            return response()->json([
                "message"=>"Wrong password"
            ]);
        }
        if(Hash::check($request->password,$user->password)) {
            $device=Device::create([
                'user_id'=>$user->id,
                'token_device'=>$request->tokenDevice
            ]);
            if($user->role == 'Admin'){
                $accessPoint=['Admin'];
                $token=$user->createToken('token',$accessPoint)->plainTextToken;
            }
            if($user->role == 'User'){
                $accessPoint=['User'];
                $token=$user->createToken('token',$accessPoint)->plainTextToken;
            }
            return response()->json([
                'message'=>'Logged in',
                'token'=>$token,
                'user'=>$user
            ]);
        }
    }
    public function logout(Request $request)
    {
        $user=$request->user();
        Device::where('user_id',$user->id)->where('token_device',$request->tokenDevice)->delete();
        $user->currentAccessToken()->delete();
        return response()->json([
            'message'=>"logout sucsses"
        ]);
    }
    public function info(Request $request){
        $user=$request->user();
        return response()->json([
            'user'=>$user
        ]);
    }

}
