<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        // Cache query kategori selama 5 menit untuk kurangi latency ke database remote
        $categories = Cache::remember('pos_categories', 300, function () {
            return Category::with(['items' => function ($query) {
                $query->where('is_available', true);
            }])->whereHas('items', function ($query) {
                $query->where('is_available', true);
            })->get();
        });

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
        $cartItemIds = collect($request->cart)->pluck('item_id')->toArray();

        // FIX #4: SELECT ... FOR UPDATE — mengunci baris item sampai COMMIT
        // sehingga transaksi paralel antre, stok tidak bisa over-sell.
        $items = Item::whereIn('id', $cartItemIds)->lockForUpdate()->get()->keyBy('id');

        // FIX #3: agregasi qty per item dulu. Tanpa ini, item yang sama muncul
        // di 2 baris keranjang masing-masing bisa lolos cek stok per-baris
        // padahal totalnya lebih besar dari stok.
        $qtyPerItem = collect($request->cart)
            ->groupBy('item_id')
            ->map(fn ($rows) => (int) $rows->sum('quantity'));

        $totalAmount = 0;

        foreach ($qtyPerItem as $itemId => $totalQty) {
            $item = $items->get($itemId);

            if (! $item) {
                throw new \Exception('Item tidak ditemukan.');
            }

            if ($item->stock < $totalQty) {
                throw new \Exception("Stok untuk menu '{$item->name}' tidak mencukupi! (Tersedia: {$item->stock}, Diminta: {$totalQty})");
            }

            $totalAmount += $item->price * $totalQty;
        }

        $change = $request->cash_received - $totalAmount;

        if ($change < 0 && $request->payment_method === 'cash') {
            throw new \Exception('Uang pembayaran kurang dari total tagihan!');
        }

        $order = Order::create([
            'invoice_number' => 'INV-'.date('ymd').'-'.rand(1000, 9999),
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'total_amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'cash_received' => $request->payment_method === 'qris' ? $totalAmount : $request->cash_received,
            'change' => $request->payment_method === 'qris' ? 0 : $change,
            'status' => 'success',
        ]);

        $orderDetails = [];
        foreach ($request->cart as $cartItem) {
            $item = $items->get($cartItem['item_id']);

            $orderDetails[] = [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'quantity' => $cartItem['quantity'],
                'price' => $item->price,
                'subtotal' => $item->price * $cartItem['quantity'],
                'notes' => $cartItem['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $item->decrement('stock', $cartItem['quantity']);
        }

        OrderDetail::insert($orderDetails);

        DB::commit();

        return redirect()->route('pos.index')->with([
            'success' => 'Transaksi sukses! Kembalian: Rp '.number_format($order->change, 0, ',', '.'),
            'order_id' => $order->id,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return back()->with('error', 'Gagal memproses transaksi: '.$e->getMessage());
    }
}

    public function struk(int $id)
    {
        $order = Order::with('orderDetails.item', 'user')->findOrFail($id);

        return view('pos.struk', compact('order'));
    }
}
