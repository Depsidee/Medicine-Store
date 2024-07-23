<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index() {
        return Company::all();
    }

    public function products($id) {
        $company=Company::find($id);
        $products=$company->products;

        if($products->count()<1) {
            return response([
                'message'=>'No products found'
            ]);
        }

        return $products;
    }
}
