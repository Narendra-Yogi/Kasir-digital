<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::with(['items' => function($query) {
            $query->where('is_available', true);
        }])->whereHas('items', function($query) {
            $query->where('is_available', true);
        })->get();

        return view('pos.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,qris',
            'cash_received' => 'required|numeric|min:0',
            'cart' => 'required|array',
            'cart.*.item_id' => 'required|exists:items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            
            foreach ($request->cart as $cartItem) {
                $item = Item::find($cartItem['item_id']);
                if ($item->stock < $cartItem['quantity']) {
                    throw new \Exception("Stok untuk menu '{$item->name}' tidak mencukupi! (Tersedia: {$item->stock})");
                }
                $totalAmount += ($item->price * $cartItem['quantity']);
            }
            $change = $request->cash_received - $totalAmount;

            if ($change < 0 && $request->payment_method === 'cash') {
                throw new \Exception("Uang pembayaran kurang dari total tagihan!");
            }

            $order = Order::create([
                'invoice_number' => 'INV-' . date('ymd') . '-' . rand(1000, 9999),
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'cash_received' => $request->payment_method === 'qris' ? $totalAmount : $request->cash_received,
                'change' => $request->payment_method === 'qris' ? 0 : $change,
                'status' => 'success',
            ]);

            foreach ($request->cart as $cartItem) {
                $item = Item::find($cartItem['item_id']);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'quantity' => $cartItem['quantity'],
                    'price' => $item->price,
                    'subtotal' => $item->price * $cartItem['quantity'],
                    'notes' => $cartItem['notes'] ?? null,
                ]); 

                if ($item->stock >= $cartItem['quantity']) {
                    $item->decrement('stock', $cartItem['quantity']);
                } else {
                    $item->update(['stock' => 0]);
                }
            }

            DB::commit();
            return redirect()->route('pos.index')->with([
                'success'  => 'Transaksi sukses! Kembalian: Rp ' . number_format($order->change, 0, ',', '.'),
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

        public function struk($id)
    {
        $order = \App\Models\Order::with('orderDetails.item', 'user')->findOrFail($id);
        return view('pos.struk', compact('order'));
    }
}
