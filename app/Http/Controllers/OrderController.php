<?php


namespace App\Http\Controllers;


use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Create a new OrderController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Create an order
     *
     * @param Request $request
     */
    function create(Request $request)
    {
        try {
            $this->validate($request, [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required'
            ], $this->messages());
        } catch (ValidationException $e) {
            $error_msg = "";
            foreach($e->errors() as $error) {
                $error_msg .= $error[0] . " ";
            }
            return response()->json(['message' => trim($error_msg)], 400);
        }

        // Check if product still has enough stock
        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');

        $product = Product::find($product_id);

        if($product->available_stock < $quantity)
            return response()->json(['message' => 'Failed to order this product due to unavailability of the stock'], 400);

        DB::transaction(function() use ($product, $product_id, $quantity) {
            try {
                // Create new order
                $order = new Order;
                $order->product_id = $product_id;
                $order->quantity = $quantity;
                $order->save();

                // Deduct order quantity from product's available stock
                $product->available_stock -= $quantity;
                $product->save();

            } catch(\Exception $e) {
                return response()->json(['message' => 'Error creating order.'], 400);
            }
        });

        return response()->json(['message' => 'You have successfully ordered this product.'], 201);
    }

    /**
     * Get the error messages for the defined validation rules
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_id.required' => 'Product Id is required',
            'quantity.required' => 'Quantity is required',
            'product_id.exists' => 'Product Id does not exist in Products'
        ];
    }

}
