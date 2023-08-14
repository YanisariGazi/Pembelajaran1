<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderAdminController extends Controller
{

    public function show()
    {
        $dataOrder = Order::all();

        return response()->json([
            'data' => $dataOrder
        ]);
    }

    public function update(Request $request, string $id)
    {
        $dataOrder = Order::findOrfail($id);

        $data = [
            'status' => $request->status,
        ];

        $dataOrder->update($data);

        return response()->json([
            'message' => 'Orderan berhasil diupdate',
            'Data' => $data
        ]);

    }


    public function delete(string $id)
    {
        $dataOrder = Order::findOrfail($id);
        
        if($dataOrder){
            
            if($dataOrder->status === 'Di Proses'){
                return response()->json(['message' => 'Order cannot be deleted because not finish.']);
            }else{
                $dataOrder->delete();
                return response()->json(['message' => 'Order deleted successfully']);
            }
        }else{
            return response()->json(['message' => 'Order not found.']);
        }
    }
}
