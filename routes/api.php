<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\medicineController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\CategoryController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\NotificationController;
use Sfolador\Devices\Controllers\DeviceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication(register___login___logout)////////////////////////////////////////////

Route::post('/register/admin',[userController::class,'registerToAdmin'])->middleware(['guest:sanctum','app_language']);
Route::post('/register/user',[userController::class,'registerToUser'])->middleware(['guest:sanctum','app_language']);
Route::post('/login',[userController::class,'login'])->middleware(['guest:sanctum','app_language']);
Route::post('/logout',[userController::class,'logout'])->middleware(['auth:sanctum','app_language']);
Route::get('/userInfo',[userController::class,'info'])->middleware(['auth:sanctum', 'app_language']);

//////////////////////////////////////////////////////////////////////////////////////////

// Product Routes

    Route::post('/addToFavorite/{id}',[ProductController::class,'addToFavorite'])->middleware(['auth:sanctum','app_language']);
    Route::get('/showFavoriteProducts',[ProductController::class,'showFavoriteProduct'])->middleware(['auth:sanctum','app_language']);
    Route::delete('/deleteFromFavorite/{id}',[ProductController::class,'deleteFromFavorite'])->middleware(['auth:sanctum','app_language']);
    Route::post('/store',[ProductController::class,'store'])->middleware(['auth:sanctum','app_language']);
    Route::get('/index',[ProductController::class,'index'])->middleware(['auth:sanctum','app_language']);
    Route::get('/category',[CategoryController::class,'index'])->middleware(['auth:sanctum','app_language']);
    Route::get('/find/{id}',[ProductController::class,'find'])->middleware(['auth:sanctum','app_language']);
    Route::get('/productsOfCategory/{id}',[CategoryController::class,'products'])->middleware(['auth:sanctum','app_language']);
    Route::get('/search/{name}',[ProductController::class,'search'])->middleware(['auth:sanctum','app_language']);
    Route::delete('/delete',[ProductController::class,'delete'])->middleware(['auth:sanctum','app_language']);
    Route::get('/company',[CompanyController::class,'index'])->middleware(['auth:sanctum','app_language']);
    Route::get('/productsOfCompany/{id}',[CompanyController::class,'products'])->middleware(['auth:sanctum','app_language']);
    Route::post('/update/{id}/{quantity}',[ProductController::class,'update'])->middleware(['auth:sanctum','app_language']);

//////////////////////////////////////////////////////////////////////////////////////////

//Order Routes

Route::post('/createOrder',[orderController::class,'createOrder'])->middleware(['auth:sanctum','app_language']);
Route::get('/myOrders',[orderController::class,'showMyOrders'])->middleware(['auth:sanctum','app_language']);
Route::get('/detailsForOrder/{id}',[orderController::class,'detailsForOrder'])->middleware(['auth:sanctum','app_language']);
Route::get('/allOrders',[orderController::class,'allOrders'])->middleware(['auth:sanctum','app_language']);
Route::post('/sendOrder/{id}',[orderController::class,'sendOrder'])->middleware(['auth:sanctum','app_language']);
Route::post('/receiveMoney/{id}',[orderController::class,'receiveMoney'])->middleware(['auth:sanctum','app_language']);
Route::post('/orderReceived/{id}',[orderController::class,'orderReceived'])->middleware(['auth:sanctum','app_language']);
Route::delete('/deleteOrder/{id}',[orderController::class,'deleteOrder'])->middleware(['auth:sanctum','app_language']);
Route::get('/allOrderHistory',[orderController::class,'allOrderHistory'])->middleware(['auth:sanctum','app_language']);
Route::get('/myOrderHistory',[orderController::class,'myOrderHistory'])->middleware(['auth:sanctum','app_language']);



/////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////notification

Route::get('/notification',[NotificationController::class,'Notification'])->middleware(['auth:sanctum','app_language']);
