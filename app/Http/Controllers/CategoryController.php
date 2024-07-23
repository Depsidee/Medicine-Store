<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index() {
        return Category::all();
    }

    public function products($id) {
        $category=Category::find($id);
        $products=$category->products;

        if($products->count()<1) {
            return response([
                'message'=>'No products found'
            ]);
        }

        return $products;
    }
}
