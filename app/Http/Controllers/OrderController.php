<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Product;
use App\Models\AddOns;
use App\Models\StoreOrder;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function addOrder(Request $request){
        $data = $request->all();
        $dataAddOns = $data['addOns'];
        $order = new Order();
        $order->customerId = $request['customerId'];
        // $order->cashierId = $request['cashierId'];
        $order->productId = $request['productId'];
        $order->quantity = $request['quantity'];
        $order->size = $request['size'];
        $order->sugarLevel = $request['sugarLevel'];
        $order->cupType = $request['cupType'];
        $order->choosenPrice = $request['choosenPrice'];
        $order->subTotal = $request['subTotal'];
        $order->status = $request['status'];
        $order->save();
        $this->addAddOns($dataAddOns, $order->id);
        return response()->json(compact('order'));
    }

    public function addAddOns($dataParams, $id){
        foreach ($dataParams as $value) {
            $addOns = new AddOns();
            $addOns['orderId'] = $id;
            $addOns['addOns'] = $value;
            $addOns->save();
        }
    }

    public function deleteOrder(Request $request){
        $order = Order::find($request->id);
        $order->delete();
        return response()->json(['success' => 'deleted successfully']);
    }

    public function retrieveOrder(Request $request){
        $order = Order::with('orderProduct')->with('sameOrder')->where('customerId', $request->id)->where('status', 'pending')->where('deleted_at', null)->get();
        return response()->json(compact('order'));
    }

    public function updateStatus(Request $request){
        $order = Order::where('customerId', $request->id)->where('deleted_at', null)->get();
        foreach ($order as $value) {
            $ord = Order::firstOrCreate(['id' => $value->id]);
            $ord->status = $request['status'];
            $ord->save();
        }
        return response()->json(['success' => 'successfully updated!']);
    }

    public function retrieveWholeOrder(Request $request){
        $order = Order::with('orderProduct')->find($request->id)->get();
        $addOns = AddOns::where('orderId', $request->id)->get(['addOns']);
        return response()->json(compact('order', 'addOns'));
    }

    public function retrieveTopProducts(Request $request){
        $prods = DB::table('orders')->leftJoin('products', 'orders.productId', '=', 'products.id')
            ->select(DB::raw('products.image as img'),DB::raw('SUM(orders.quantity) as quan'),DB::raw('products.productName as pName'))
            ->groupBy('img','pName')
            ->orderBy('quan', 'desc')
            ->get();
        return response()->JSON(compact('prods'));
    }
}
