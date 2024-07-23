<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\GenericName;
use Illuminate\Http\Request;
use App\Models\Product;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
    public function index() {
       return Product::with('category','company','genericName')->where('Show',0)->get();
    }

    public function store(Request $request) {
        $user=$request->user();
        if(!$user->tokenCan('Admin')){
            return response()->json([
                'message'=>__('msg.onlyAdmin')
            ]);
        }
        $year=date('Y');
        $month=date('M');
        $day=date('D');
        $request->validate([
            'tradeName'=>'required|string|unique:products,tradeName',
            'genericName'=>'required|string',
            'company'=>'required|string',
            'category'=>'required|string',
            'price'=>'required|integer',
            'amount'=>'required|integer',
            'expiringYear'=>'required|integer',
            'expiringMonth'=>'required|integer',
            'expiringDay'=>'required|integer',
        ]);

        if($request['expiringYear']<$year||($request['expiringYear']==$year&&$request['expiringMonth']<$month)||($request['expiringYear']==$year&&$request['expiringMonth']==$month&&$request['expiringDay']<$day)) {
            return response([
                'message'=>"This product is expired and can't be added"
            ]);
        }
        if($request['expiringMonth']>12||$request['expiringMonth']<1) {
            return response([
                'message'=>'The expiring month is not correct'
            ]);
        }
        if($request['expiringDay']>31||$request['expiringDay']<1) {
            return response([
                'message'=>'The expiring day is not correct'
            ]);
        }
        if($request['amount']<1) {
            return response([
                'message'=>'The amount must be a positive number'
            ]);
        }

        $category=Category::firstOrCreate(['name'=>$request['category']]);
        $company=Company::firstOrCreate(['name'=>$request['company']]);
        $genericName=GenericName::firstOrCreate(['name'=>$request['genericName']]);
        return Product::create([
            'tradeName'=>$request['tradeName'],
            'price'=>$request['price'],
            'amount'=>$request['amount'],
            'expiringYear'=>$request['expiringYear'],
            'expiringMonth'=>$request['expiringMonth'],
            'expiringDay'=>$request['expiringDay'],
            'category_id'=>$category->id,
            'company_id'=>$company->id,
            'generic_name_id'=>$genericName->id,
            'Show'=>0

        ]);
        return response()->json([
            'message'=>'The product has been created successfully'
        ]);
    }

    public function find($id) {
        $product=Product::with('category','company','genericName')->get()->where('id',$id);
        if($product->count()<1) {
            return response([
                'message'=>'The id is not correct'
            ]);
        }

            return $product->first();
    }

    public function search($name)
    {
        $product=Product::with('category','company','genericName')->where('tradeName','like','%'.$name.'%')->get();
        if($product->count()>0) {
        return $product;
        }

        $genericNames=GenericName::where('name','like','%'.$name.'%')->get();
        if($genericNames->count()>0) {
            $products=array();
            foreach($genericNames as $genericName) {
                foreach($genericName->products as $product) {
                    $products[]=$product;
                }
            }
            return $products;
        }

        $categories=Category::where('name','like','%'.$name.'%')->get();
        if($categories->count()>0) {
            $products=array();
            foreach($categories as $category) {
                foreach($category->products as $product) {
                    $products[]=$product;
                }
            }
            return $products;
        }

        return response([
            'message'=>'No products found'
        ]);
    }

    public function update(Request $request,$id,$quantity) {
        $user=$request->user();
        if(!$user->tokenCan('Admin')){
            return response()->json([
                'message'=>__('msg.onlyAdmin')
            ]);
        }
        $product=Product::with('category','company','genericName')->find($id);
        if($product) {
            $product->increment('amount',$quantity);
            $product->save();
            return $product;
        }

        return response([
            'message'=>'The id is not correct'
        ]);

    }

    public function delete(Request $request) {
        $user=$request->user();
        if(!$user->tokenCan('Admin')){
            return response()->json([
                'message'=>__('msg.onlyAdmin')
            ]);
        }
        $year=date('Y');
        $month=date('M');
        $day=date('D');
        $products=Product::all();
        foreach($products as $product) {
            if($product['amount']<1||$product['expiringYear']<$year||($product['expiringYear']==$year&&$product['expiringMonth']<$month)||($product['expiringYear']==$year&&$product['expiringMonth']==$month&&$product['expiringDay']<$day)) {
                Product::where('id',$product['id'])->delete();
            }
        }

        return response([
            'message'=>'Expired products have been deleted successfully'
        ]);
    }
    public function addToFavorite($id,Request $request){
        $user=$request->user();
        if(!$user->tokenCan('User')){
            return response()->json([
                'message'=>__('msg.onlyUser')
            ]);
        }
        $user_id=auth()->user()->id;
        $medicine_id=$id;
        $status=DB::table('product_user')->where('user_id',$user_id)->where('product_id',$medicine_id)->count();
        if($status==0){
            $status=DB::table('product_user')->insert([
                'user_id'=>$user_id,
                'product_id'=>$medicine_id
            ]);
            return Response()->json([
                'messagge'=>__('msg.addToFavorite')
            ],200);
        }else{
            return Response()->json([
                'messagge'=>__('msg.addToFavoriteAlready')
            ],200);
        }
    }
    public function showFavoriteProduct(Request $request){
        $user=$request->user();
        if(!$user->tokenCan('User')){
            return response()->json([
                'message'=>__('msg.onlyUser')
            ]);
        }
        $user=auth()->user();
        if($user->favoriteProducts->count()==0){
            return response()->json([
                'message'=>'you dont add any product to favorite'
            ]);
        }
        return response()->json([
            'favoriteProduct'=>$user->favoriteProducts
        ]);
    }
    public function deleteFromFavorite($id,Request $request){
        $user=$request->user();
        if(!$user->tokenCan('User')){
            return response()->json([
                'message'=>__('msg.onlyUser')
            ]);
        }
        $user_id=auth()->user()->id;
        $medicine_id=$id;
        $status=DB::table('product_user')->where('user_id',$user_id)->where('product_id',$medicine_id)->delete();
        if(!$status){
            return response()->json([
                'message'=>__('msg.NotFoundInFavorite')
            ],200);
        }else{
            return response()->json([
                'message'=>__('msg.deleteFromFavorite')
            ],200);
        }
    }
}
