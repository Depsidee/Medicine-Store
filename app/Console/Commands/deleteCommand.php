<?php

namespace App\Console\Commands;


use App\Models\Product;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class deleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:command';

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
        $time=Carbon::now();
        $products=Product::all();
        foreach($products as $product){
            if($time->year > $product['expiringYear']||
            $time->year == $product['expiringYear']&& $time->month > $product['expiringMonth']||
            $time->year == $product['expiringYear']&& $time->month == $product['expiringMonth'] && $time->day > $product['expiringDay']){
                Product::where('id',$product->id)->update([
                    'Show'=>1
                ]);
            }
        }
    }
}
