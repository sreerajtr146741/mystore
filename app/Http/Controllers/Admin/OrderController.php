<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {

            $hasOrders    = Schema::hasTable('orders');
            $hasStatusCol = $hasOrders && Schema::hasColumn('orders', 'status');

            // Filters
            $status = $request->string('status')->toString();

            if ($hasOrders) {
                $q = DB::table('orders')->select('id','user_id','total','status','created_at')->latest('id');

                if ($status !== '') {
                    $q->where('status', $status);
                }

                $orders = $q->paginate(12);
                $pendingCount = $hasStatusCol ? DB::table('orders')->where('status','pending')->count() : 0;
            } else {
                // Demo fallback if you don't have an orders table yet
                $orders = collect([
                    (object)['id'=>1001,'user_id'=>4,'total'=>2599,'status'=>'pending','created_at'=>now()],
                    (object)['id'=>1000,'user_id'=>3,'total'=>4899,'status'=>'paid','created_at'=>now()->subDay()],
                ]);
                $pendingCount = $orders->where('status','pending')->count();
            }

            return view('admin.orders', compact('orders','pendingCount','status','hasOrders'));

        } catch (\Exception $e) {

            \Log::error('OrderController index error: '.$e->getMessage());

            return back()->with('error', 'Unable to load orders. Please try again.');
        }
    }
}
