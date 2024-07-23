<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class EndAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'end-amount:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $products=Product::all();
        foreach($products as $product){
            if($product['amount']<1){
                $product->delete();
            }
        }
    }
}
