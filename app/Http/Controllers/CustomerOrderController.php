<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $orders = Order::with('orderItems.product')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'orders' => $orders
            ]);
        }

        $categories = Category::where('available', true)
            ->with(['products' => function($query) {
                $query->where('available', true);
            }])
            ->get();

        return Inertia::render('CustomerOrder', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'mnt_recu' => 'required|numeric|min:0',
                'discount' => 'sometimes|numeric|min:0'
            ]);

            \DB::beginTransaction();

            try {
                $total = 0;
                $items = [];

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    if ($product->available != 1) {
                        throw new \Exception("المنتج {$product->name} غير متاح حاليًا");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;

                    $items[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $subtotal
                    ];
                }

                

                $total = round($total, 2);
                $discount = round($request->discount ?? 0, 2);
                $mnt_recu = round($request->mnt_recu, 2);
                $finalTotal = max(0, round($total - $discount, 2));



                if ($mnt_recu < $finalTotal) {
                    throw new \Exception(
                        "disount" .
                        "{$discount} "
                    );
                }

                $order = Order::create([
                    'total' => $total,
                    'discount' => $discount,
                    'mnt_recu' => $mnt_recu,
                    'mnt_rendu' => round($mnt_recu - $finalTotal, 2),
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                foreach ($items as $item) {
                    $order->orderItems()->create([
                        'product_id' => $item['product']->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['subtotal']
                    ]);
                }

                \DB::commit();

                return response()->json([
                    'success' => true,
                    'order' => $order->load('orderItems.product'),
                    'message' => 'تم إنشاء الطلب بنجاح'
                ], 201);

            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getReceipt($id)
    {
        $order = Order::with(['orderItems.product'])->findOrFail($id);



        return response()->json([
            'success' => true,
            'receipt' => [
                'order_id' => $order->id,
                'date' => $order->created_at->format('Y-m-d H:i:s'),
                'items' => $order->orderItems->map(function($item) {
                    return [
                        'name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ];
                }),
                'subtotal' => ($order->total + $order->discount),
                'discount' => $order->discount,
                'total' => $order->total ,
                'amount_received' => $order->mnt_recu,
                'change' => $order->mnt_rendu
            ]
        ]);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }
}
