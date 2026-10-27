@extends('layouts.app')

@section('title', 'Kasir (POS)')

{{-- Page Title is not yielded because we override the header entirely for POS --}}

@section('content')

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'success',
            title: 'Pembayaran berhasil',
            text: '{!! session("success") !!}',
            showCancelButton: true,
            confirmButtonColor: '#931b13',
            cancelButtonColor: '#57504a',
            confirmButtonText: 'Cetak Struk',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(`/kasir/struk/{{ session('order_id') }}`, '_blank');
            }
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{!! session("error") !!}',
            confirmButtonColor: '#931b13',
        });
    });
</script>
@endif

<div class="flex-1 flex flex-col lg:flex-row overflow-hidden relative h-full min-h-0 bg-surface">
    {{-- Menu Grid Area --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Custom POS Header --}}
        <div class="px-4 lg:px-8 pt-6 pb-4 shrink-0">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-[#efe7d8] text-[#57504a] transition-colors shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div>
                        <p class="text-xs text-[#8a8179]">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                        <h2 class="font-display text-[22px] lg:text-[26px] font-semibold tracking-tight text-[#1c1410] leading-tight">Halo, {{ explode(' ', auth()->user()->name)[0] }}</h2>
                        <p class="text-[13px] text-[#8a8179] mt-0.5">Pilih menu yang dipesan pelanggan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2.5 w-full md:w-auto">
                    <div class="relative flex-1 md:w-64">
                        <svg class="w-[18px] h-[18px] absolute left-3 top-3 text-[#b3a897]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" id="searchInput" onkeyup="filterMenu()" class="field pl-10" placeholder="Cari menu…">
                    </div>
                </div>
            </div>

            {{-- Category Pills --}}
            <div class="flex gap-2 overflow-x-auto pb-1">
                <button onclick="filterCategory('all', this)" class="category-btn is-active px-4 py-2 text-[13px] font-medium whitespace-nowrap">
                    Semua menu
                </button>
                @foreach($categories as $category)
                    <button onclick="filterCategory({{ $category->id }}, this)" class="category-btn px-4 py-2 text-[13px] font-medium whitespace-nowrap">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto px-4 lg:px-8 pb-24 lg:pb-8">
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6" id="menuGrid">
                @foreach($categories as $category)
                    @foreach($category->items as $item)
                    <div class="menu-card p-3 {{ $item->stock === 0 ? 'opacity-60 select-none' : '' }} flex flex-col h-full relative"
                         data-category="{{ $category->id }}"
                         data-name="{{ strtolower($item->name) }}">

                        {{-- Image Area --}}
                        <div class="relative w-full h-36 lg:h-44 rounded-[10px] overflow-hidden bg-[#f4eee2] mb-3">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover {{ $item->stock > 0 ? 'group-hover:scale-[1.03]' : '' }} transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[#c9bda9]">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif

                            {{-- Add to Cart Floating Button --}}
                            @if($item->stock > 0)
                            <button onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }}, '{{ $item->image ? asset('storage/'.$item->image) : '' }}', {{ $item->stock }})" class="absolute top-2 right-2 w-8 h-8 rounded-lg bg-[#fffdf9]/95 border border-[#e8e0d3] text-[#1c1410] flex items-center justify-center hover:bg-[#1c1410] hover:text-[#f7f1e6] hover:border-[#1c1410] transition-colors z-10 active:scale-95" aria-label="Tambah {{ $item->name }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"></path></svg>
                            </button>
                            @else
                            <div class="absolute top-2 right-2 px-2.5 py-1 rounded-md bg-[#1c1410] text-[#f7f1e6] font-medium text-[11px] z-10 select-none pointer-events-none">
                                Habis
                            </div>
                            @endif
                        </div>
                        
                        {{-- Text Area --}}
                        <div class="flex flex-col flex-1">
                            <h3 class="font-medium text-[#1c1410] text-sm lg:text-[15px] leading-snug mb-1">{{ $item->name }}</h3>

                            <p class="text-xs mb-3">
                                @if($item->stock > 5)
                                    <span class="inline-flex items-center gap-1.5 text-[#57504a]"><span class="w-1.5 h-1.5 rounded-full bg-[#1a6b3c] inline-block"></span>Stok {{ $item->stock }}</span>
                                @elseif($item->stock > 0)
                                    <span class="inline-flex items-center gap-1.5 text-[#931b13]"><span class="w-1.5 h-1.5 rounded-full bg-[#b42318] inline-block"></span>Sisa {{ $item->stock }}</span>
                                @else
                                    <span class="text-[#8a8179]">Stok habis</span>
                                @endif
                                <span class="text-[#b3a897]"> · {{ $category->name }}</span>
                            </p>

                            <div class="mt-auto flex items-baseline justify-between">
                                <span class="font-display font-semibold text-[#1c1410] text-[15px] lg:text-lg tnum">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- Cart Toggle Button (Mobile) --}}
    <button id="cart-toggle-btn" onclick="toggleCart()" class="lg:hidden fixed bottom-4 right-4 z-30 bg-[#1c1410] text-[#f7f1e6] w-14 h-14 rounded-full flex items-center justify-center transition-transform active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        <span id="cart-badge" class="absolute -top-1 -right-1 bg-[#b42318] text-white text-xs font-semibold rounded-full w-5 h-5 flex items-center justify-center hidden border-2 border-[#faf7f2]">0</span>
    </button>

    {{-- Cart Overlay (Mobile) --}}
    <div id="cart-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden opacity-0 pointer-events-none transition-opacity" onclick="toggleCart()"></div>

    {{-- Cart Sidebar --}}
    <aside id="cart-panel" class="fixed lg:static bottom-0 left-0 right-0 lg:bottom-auto lg:left-auto lg:right-auto w-full lg:w-[380px] max-h-[85vh] lg:max-h-none h-full bg-[#fffdf9] border-t lg:border-t-0 lg:border-l border-[#e8e0d3] flex flex-col shrink-0 z-50 overflow-hidden lg:translate-y-0 translate-y-full transition-transform duration-300 ease-in-out">
        <div class="px-6 pt-6 pb-4 border-b border-[#f1ebe0] shrink-0 bg-[#fffdf9] relative">
            {{-- Drag handle (mobile) --}}
            <div class="absolute top-2 left-1/2 -translate-x-1/2 w-10 h-1 bg-[#e8e0d3] rounded-full lg:hidden"></div>

            <h2 class="font-display text-lg font-semibold tracking-tight">Pesanan saat ini</h2>
            <p class="text-xs text-[#8a8179] mt-0.5">Periksa sebelum ke pembayaran</p>
        </div>

        <div id="cart-items-container" class="flex-1 overflow-y-auto px-6 py-5 flex flex-col gap-4">
            <div class="text-center text-[#8a8179] text-sm mt-10">Keranjang masih kosong</div>
        </div>

        <div class="p-6 pt-4 shrink-0 mt-auto border-t border-[#f1ebe0]">
            <div class="flex justify-between items-baseline mb-1.5">
                <span class="text-[13px] text-[#8a8179]">Subtotal</span>
                <span class="text-sm font-medium tnum" id="sidebar-subtotal-display">Rp 0</span>
            </div>
            <div class="flex justify-between items-baseline mb-4">
                <span class="text-sm font-medium">Total</span>
                <span class="font-display text-2xl font-semibold tnum" id="sidebar-total-display">Rp 0</span>
            </div>
            <button type="button" id="btn-checkout" disabled onclick="openCheckoutModal()" class="btn-ink w-full py-3.5 text-sm flex items-center justify-center gap-2">
                Lanjut ke pembayaran
            </button>
            <button type="button" onclick="resetCart()" id="btn-reset" class="hidden w-full mt-2 py-2 text-[13px] text-[#8a8179] hover:text-[#931b13] transition-colors">Kosongkan keranjang</button>
        </div>
    </aside>
</div>

{{-- Checkout Modal --}}
<div id="checkout-modal" class="fixed inset-0 bg-black/50 z-[60] hidden flex items-center justify-center transition-opacity opacity-0 duration-300" onclick="closeCheckoutModalOnOverlay(event)">
    <div class="bg-[#fffdf9] w-full max-w-md rounded-[16px] border border-[#e8e0d3] flex flex-col max-h-[90vh] mx-4 transition-transform scale-95 duration-300 overflow-hidden" id="checkout-modal-content">
        <div class="px-6 py-5 border-b border-[#f1ebe0] flex justify-between items-center shrink-0">
            <div>
                <h2 class="font-display text-lg font-semibold tracking-tight">Pembayaran</h2>
                <p class="text-xs text-[#8a8179] mt-0.5">Konfirmasi nominal sebelum proses</p>
            </div>
            <button onclick="closeCheckoutModal()" class="p-2 rounded-lg hover:bg-[#f4eee2] text-[#57504a] transition-colors" aria-label="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-6 overflow-y-auto">
            <form action="{{ route('pos.store') }}" method="POST" id="pos-form">
                @csrf
                <div id="hidden-cart-inputs"></div>

                <div class="space-y-4 mb-6">
                    <input type="text" name="customer_name" placeholder="Nama pelanggan (opsional)" class="field">

                    <select name="payment_method" id="payment_method" class="field cursor-pointer">
                        <option value="cash">Tunai</option>
                        <option value="qris">QRIS</option>
                    </select>

                    <div class="pt-4 border-t border-[#f1ebe0] space-y-3">
                        <div class="flex justify-between items-baseline bg-[#f7f2e9] px-4 py-3.5 rounded-[10px] border border-[#e8e0d3]">
                            <span class="text-[13px] text-[#57504a]">Total tagihan</span>
                            <span class="font-display text-xl font-semibold tnum" id="total-price-display">Rp 0</span>
                        </div>

                        <div id="cash-section">
                            <div class="flex flex-col gap-3">
                                <div class="flex justify-between items-center mt-1">
                                    <label class="text-[13px] font-medium text-[#1c1410] whitespace-nowrap">Uang diterima</label>
                                    <input type="hidden" name="cash_received" id="cash_received_hidden">
                                    <div class="w-52 flex items-center border border-[#e8e0d3] rounded-[10px] bg-white focus-within:border-[#b42318] transition-colors overflow-hidden">
                                        <span class="pl-4 py-2.5 text-sm font-medium text-[#8a8179] select-none">Rp</span>
                                        <input type="text" id="cash_received" inputmode="numeric" placeholder="0" class="w-full pr-4 py-2.5 text-lg text-right font-semibold tnum text-[#1c1410] bg-transparent focus:outline-none focus-visible:outline-none focus:ring-0 outline-none border-none shadow-none" style="outline: none !important;" required oninput="formatCashInput(this); calculateChange()">
                                    </div>
                                </div>

                                {{-- Segmented Control Pilihan Input Pembayaran --}}
                                <div class="flex bg-[#f4eee2] p-1 rounded-[10px] mt-1 text-xs font-medium border border-[#e8e0d3]">
                                    <button type="button" onclick="switchPaymentInput('quick', this)" id="tab-quick-cash" class="flex-1 py-2 rounded-lg bg-white text-[#1c1410] transition-all text-center cursor-pointer flex items-center justify-center gap-1.5 border border-[#e8e0d3]">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="2" y="6" width="20" height="12" rx="2" />
                                            <circle cx="12" cy="12" r="3" />
                                            <path d="M6 12h.01M18 12h.01" />
                                        </svg>
                                        <span>Uang pecahan</span>
                                    </button>
                                    <button type="button" onclick="switchPaymentInput('numpad', this)" id="tab-numpad-cash" class="flex-1 py-2 rounded-lg text-[#8a8179] hover:text-[#1c1410] transition-all text-center cursor-pointer flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="4" y="3" width="16" height="18" rx="2" />
                                            <path d="M8 7h8M8 11h2M14 11h2M8 15h2M14 15h2" stroke-linecap="round" />
                                        </svg>
                                        <span>Numpad manual</span>
                                    </button>
                                </div>

                                {{-- OPSI 1: Grid Uang Pecahan Bawaan (Tampilan Awal Default) --}}
                                <div id="quick-cash-grid" class="grid grid-cols-3 gap-2 mt-1">
                                    <button type="button" onclick="setQuickCash('pas')" class="col-span-3 py-2.5 bg-[#17110c] hover:bg-[#2c231c] text-[#f7f1e6] font-medium text-[13px] rounded-[10px] transition-colors active:scale-[0.99] cursor-pointer">
                                        Uang pas
                                    </button>
                                    <button type="button" onclick="setQuickCash(10000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">10rb</button>
                                    <button type="button" onclick="setQuickCash(20000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">20rb</button>
                                    <button type="button" onclick="setQuickCash(30000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">30rb</button>
                                    <button type="button" onclick="setQuickCash(40000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">40rb</button>
                                    <button type="button" onclick="setQuickCash(50000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">50rb</button>
                                    <button type="button" onclick="setQuickCash(75000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">75rb</button>
                                    <button type="button" onclick="setQuickCash(100000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">100rb</button>
                                    <button type="button" onclick="setQuickCash(150000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">150rb</button>
                                    <button type="button" onclick="setQuickCash(200000)" class="py-2.5 bg-white hover:bg-[#f7f2e9] text-[#1c1410] font-medium text-[13px] tnum rounded-[10px] border border-[#e8e0d3] transition-colors active:scale-[0.99] cursor-pointer">200rb</button>
                                </div>

                                {{-- OPSI 2: Kalkulator Numpad Manual (Disembunyikan di Awal) --}}
                                <div id="numpad-grid" class="grid grid-cols-4 gap-2 mt-1 hidden">
                                    <button type="button" onclick="appendNumpad('1')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">1</button>
                                    <button type="button" onclick="appendNumpad('2')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">2</button>
                                    <button type="button" onclick="appendNumpad('3')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">3</button>
                                    <button type="button" onclick="appendNumpad('000')" class="py-3 bg-[#f7f2e9] text-[#1c1410] font-medium text-base tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#efe7d8] active:scale-[0.98] transition-transform cursor-pointer">000</button>

                                    <button type="button" onclick="appendNumpad('4')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">4</button>
                                    <button type="button" onclick="appendNumpad('5')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">5</button>
                                    <button type="button" onclick="appendNumpad('6')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">6</button>
                                    <button type="button" onclick="appendNumpad('C')" class="py-3 bg-[#fdf0ee] text-[#931b13] font-medium text-lg rounded-[10px] border border-[#f2b3a6] hover:bg-[#f9dcd6] active:scale-[0.98] transition-transform cursor-pointer">C</button>

                                    <button type="button" onclick="appendNumpad('7')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">7</button>
                                    <button type="button" onclick="appendNumpad('8')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">8</button>
                                    <button type="button" onclick="appendNumpad('9')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">9</button>
                                    <button type="button" onclick="appendNumpad('0')" class="py-3 bg-white text-[#1c1410] font-medium text-lg tnum rounded-[10px] border border-[#e8e0d3] hover:bg-[#f7f2e9] active:scale-[0.98] transition-transform cursor-pointer">0</button>
                                </div>
                            </div>

                            <div class="flex justify-between items-baseline mt-5 pt-4 border-t border-dashed border-[#e8e0d3]">
                                <span class="text-[13px] text-[#57504a]">Kembalian</span>
                                <span class="font-display text-xl font-semibold tnum" id="change-display">Rp 0</span>
                            </div>
                            <p id="cash-warning" class="text-xs text-[#931b13] mt-1 hidden text-right">Nominal belum mencukupi.</p>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="btn-submit" disabled onclick="confirmTransaction()" class="btn-ink w-full py-3.5 text-sm flex items-center justify-center gap-2 mt-2">
                    Konfirmasi pembayaran
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // --- PENUKARAN OPSI INPUT PEMBAYARAN TUNAI ---
    function switchPaymentInput(type, tabBtn) {
        const quickGrid = document.getElementById('quick-cash-grid');
        const numpadGrid = document.getElementById('numpad-grid');
        const tabQuick = document.getElementById('tab-quick-cash');
        const tabNumpad = document.getElementById('tab-numpad-cash');

        if (type === 'quick') {
            quickGrid.classList.remove('hidden');
            numpadGrid.classList.add('hidden');

            tabQuick.classList.add('bg-white', 'text-[#1c1410]', 'border', 'border-[#e8e0d3]');
            tabQuick.classList.remove('text-[#8a8179]');

            tabNumpad.classList.remove('bg-white', 'text-[#1c1410]', 'border', 'border-[#e8e0d3]');
            tabNumpad.classList.add('text-[#8a8179]');
        } else {
            quickGrid.classList.add('hidden');
            numpadGrid.classList.remove('hidden');

            tabNumpad.classList.add('bg-white', 'text-[#1c1410]', 'border', 'border-[#e8e0d3]');
            tabNumpad.classList.remove('text-[#8a8179]');

            tabQuick.classList.remove('bg-white', 'text-[#1c1410]', 'border', 'border-[#e8e0d3]');
            tabQuick.classList.add('text-[#8a8179]');
        }
    }

    // --- CART PANEL TOGGLE (MOBILE) ---
    let cartOpen = false;
    function toggleCart() {
        const panel = document.getElementById('cart-panel');
        const overlay = document.getElementById('cart-overlay');
        cartOpen = !cartOpen;

        if (cartOpen) {
            panel.classList.remove('translate-y-full');
            panel.classList.add('translate-y-0');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
        } else {
            panel.classList.add('translate-y-full');
            panel.classList.remove('translate-y-0');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
        }
    }

    function updateCartBadge() {
        const badge = document.getElementById('cart-badge');
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        if (totalItems > 0) {
            badge.textContent = totalItems;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // --- 1. LOGIKA FILTER PENCARIAN & KATEGORI ---
    function filterMenu() {
        let keyword = document.getElementById('searchInput').value.toLowerCase();
        let cards = document.querySelectorAll('.menu-card');
        
        cards.forEach(card => {
            let name = card.getAttribute('data-name');
            if(name.includes(keyword)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterCategory(categoryId, btnElement) {
        // Reset semua tombol kategori
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.classList.remove('is-active');
        });

        // Aktifkan tombol yang diklik
        btnElement.classList.add('is-active');

        // Sembunyikan/Tampilkan kartu menu
        let cards = document.querySelectorAll('.menu-card');
        cards.forEach(card => {
            if(categoryId === 'all' || card.getAttribute('data-category') == categoryId) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
        
        document.getElementById('searchInput').value = '';
    }

    // --- 2. LOGIKA KERANJANG BELANJA (CART) ---
    let cart = [];

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function formatCashInput(el) {
        let value = el.value.replace(/\D/g, '');
        el.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        document.getElementById('cash_received_hidden').value = value ? parseInt(value) : 0;
    }

    function getCashValue() {
        return parseInt(document.getElementById('cash_received_hidden').value) || 0;
    }

    function setQuickCash(amount) {
        let input = document.getElementById('cash_received');
        if (amount === 'pas') {
            input.value = currentTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        } else {
            input.value = amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        formatCashInput(input);
        calculateChange();
    }

    function appendNumpad(char) {
        let input = document.getElementById('cash_received');
        let currentVal = input.value.replace(/\D/g, '');
        
        if (char === 'C') {
            currentVal = '';
        } else {
            currentVal += char;
        }
        
        input.value = currentVal;
        formatCashInput(input);
        calculateChange();
    }

    function addToCart(id, name, price, image, stock) {
        let existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            if (existingItem.qty >= stock) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Stok tidak mencukupi! Maksimal pembelian: ' + stock
                });
                return;
            }
            existingItem.qty += 1;
        } 
        else {
            cart.push({ id: id, name: name, price: price, image: image, qty: 1, stock: stock });
        }
        updateCartUI();
    }

    function changeQty(id, delta) {
        let item = cart.find(item => item.id === id);
        if (item) {
            if (delta > 0 && item.qty >= item.stock) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Stok tidak mencukupi! Maksimal pembelian: ' + item.stock
                });
                return;
            }
            item.qty += delta;
            if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
            updateCartUI();
        }
    }

    function resetCart() {
        if (cart.length === 0) return;
        Swal.fire({
            title: 'Kosongkan keranjang?',
            text: 'Semua item di keranjang akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#931b13',
            cancelButtonColor: '#57504a',
            confirmButtonText: 'Ya, kosongkan'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                updateCartUI();
            }
        });
    }

    let currentTotal = 0;

    function openCheckoutModal() {
        if (cart.length === 0) return;
        
        // Reset tab input kasir ke pilihan default 'Uang Pecahan'
        switchPaymentInput('quick');
        
        const modal = document.getElementById('checkout-modal');
        const content = document.getElementById('checkout-modal-content');
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        content.classList.remove('scale-95');
        
        setTimeout(() => {
            if (document.getElementById('payment_method').value === 'cash') {
                document.getElementById('cash_received').focus();
            }
        }, 100);
    }

    function closeCheckoutModal() {
        const modal = document.getElementById('checkout-modal');
        const content = document.getElementById('checkout-modal-content');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function closeCheckoutModalOnOverlay(e) {
        if (e.target.id === 'checkout-modal') {
            closeCheckoutModal();
        }
    }

    function updateCartUI() {
        const container = document.getElementById('cart-items-container');
        const totalDisplay = document.getElementById('total-price-display');
        const sidebarSubtotalDisplay = document.getElementById('sidebar-subtotal-display');
        const sidebarTotalDisplay = document.getElementById('sidebar-total-display');
        const btnCheckout = document.getElementById('btn-checkout');
        const hiddenInputs = document.getElementById('hidden-cart-inputs');
        const inputCash = document.getElementById('cash_received');
        
        container.innerHTML = ''; hiddenInputs.innerHTML = '';
        currentTotal = 0;

        if (cart.length === 0) {
            container.innerHTML = '<div class="text-center text-[#8a8179] text-sm mt-10">Keranjang masih kosong</div>';
            totalDisplay.innerText = 'Rp 0';
            sidebarSubtotalDisplay.innerText = 'Rp 0';
            sidebarTotalDisplay.innerText = 'Rp 0';
            inputCash.value = '';
            currentTotal = 0;
            document.getElementById('btn-reset').classList.add('hidden');
            btnCheckout.disabled = true;
            calculateChange();
            updateCartBadge();
            
            const modal = document.getElementById('checkout-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closeCheckoutModal();
            }
            return;
        }

        cart.forEach((item, index) => {
            currentTotal += (item.price * item.qty);
            
            let imgHtml = item.image ?
                `<img src="${item.image}" class="w-14 h-14 rounded-[10px] object-cover shrink-0 border border-[#e8e0d3]">` :
                `<div class="w-14 h-14 rounded-[10px] bg-[#f4eee2] flex items-center justify-center text-[#b3a897] shrink-0 border border-[#e8e0d3]"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>`;

            container.innerHTML += `
                <div class="flex gap-3 items-center">
                    ${imgHtml}
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-[13px] text-[#1c1410] leading-tight truncate">${item.name}</h4>
                        <p class="font-medium tnum text-[#1c1410] text-[13px] mt-1">${formatRupiah(item.price)}</p>
                    </div>
                    <div class="flex items-center gap-1.5 bg-[#f7f2e9] rounded-lg p-1 border border-[#e8e0d3] shrink-0">
                        <button type="button" onclick="changeQty(${item.id}, -1)" class="w-6 h-6 rounded-md bg-white border border-[#e8e0d3] text-[#1c1410] hover:bg-[#efe7d8] flex items-center justify-center transition-colors cursor-pointer font-medium select-none">-</button>
                        <span class="font-medium text-xs tnum text-[#1c1410] w-4 text-center">${item.qty}</span>
                        <button type="button" onclick="changeQty(${item.id}, 1)" class="w-6 h-6 rounded-md bg-[#1c1410] text-[#f7f1e6] hover:bg-[#2c231c] flex items-center justify-center transition-colors cursor-pointer font-medium select-none">+</button>
                    </div>
                </div>
            `;
            hiddenInputs.innerHTML += `
                <input type="hidden" name="cart[${index}][item_id]" value="${item.id}">
                <input type="hidden" name="cart[${index}][quantity]" value="${item.qty}">
            `;
        });

        totalDisplay.innerText = formatRupiah(currentTotal);
        sidebarSubtotalDisplay.innerText = formatRupiah(currentTotal);
        sidebarTotalDisplay.innerText = formatRupiah(currentTotal);
        document.getElementById('btn-reset').classList.remove('hidden');
        btnCheckout.disabled = false;
        calculateChange();
        updateCartBadge();
    }

    function calculateChange() {
        const inputCash = document.getElementById('cash_received');
        const changeDisplay = document.getElementById('change-display');
        const warning = document.getElementById('cash-warning');
        const btnSubmit = document.getElementById('btn-submit');
        const paymentMethod = document.getElementById('payment_method').value;

        if (paymentMethod === 'qris') {
            btnSubmit.disabled = cart.length === 0;
            return;
        }

        if (cart.length === 0) {
            changeDisplay.innerText = 'Rp 0';
            changeDisplay.className = 'font-display text-xl font-semibold tnum text-[#b3a897]';
            warning.classList.add('hidden');
            btnSubmit.disabled = true;
            return;
        }

        const cashReceived = getCashValue();
        const change = cashReceived - currentTotal;

        if (cashReceived === 0) {
            changeDisplay.innerText = 'Rp 0';
            changeDisplay.className = 'font-display text-xl font-semibold tnum text-[#b3a897]';
            warning.classList.add('hidden');
            btnSubmit.disabled = true;
        } else if (change < 0) {
            changeDisplay.innerText = '- ' + formatRupiah(Math.abs(change));
            changeDisplay.className = 'font-display text-xl font-semibold tnum text-[#931b13]';
            warning.classList.remove('hidden');
            btnSubmit.disabled = true;
        } else {
            changeDisplay.innerText = formatRupiah(change);
            changeDisplay.className = 'font-display text-xl font-semibold tnum text-[#1a6b3c]';
            warning.classList.add('hidden');
            btnSubmit.disabled = false;
        }
    }

    document.getElementById('payment_method').addEventListener('change', function(e) {
        const inputCash = document.getElementById('cash_received');
        const hiddenCash = document.getElementById('cash_received_hidden');
        const cashSection = document.getElementById('cash-section');
        if(e.target.value === 'qris') {
            cashSection.style.display = 'none';
            inputCash.removeAttribute('required');
            inputCash.value = '';
            hiddenCash.value = 0;
        } else {
            cashSection.style.display = 'block';
            inputCash.setAttribute('required', 'required');
            inputCash.value = '';
            hiddenCash.value = '';
        }
        calculateChange();
    });

    function confirmTransaction() {
        const paymentMethod = document.getElementById('payment_method').value;
        const cashReceived = getCashValue();
        const change = cashReceived - currentTotal;

        let itemsHtml = cart.map(item =>
            `<tr>
                <td class="text-left py-1 text-sm">${item.name}</td>
                <td class="text-center py-1 text-sm">${item.qty}</td>
                <td class="text-right py-1 text-sm">${formatRupiah(item.price * item.qty)}</td>
            </tr>`
        ).join('');

        let receiptHtml = `
            <div style="text-align:left; font-size:13px;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px dashed #ccc;">
                            <th style="text-align:left; padding:4px 0; font-size:11px; color:#888;">ITEM</th>
                            <th style="text-align:center; padding:4px 0; font-size:11px; color:#888;">QTY</th>
                            <th style="text-align:right; padding:4px 0; font-size:11px; color:#888;">SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>${itemsHtml}</tbody>
                </table>
                <div style="border-top:1px dashed #ccc; margin-top:8px; padding-top:8px;">
                    <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:15px; color:#111;">
                        <span>TOTAL</span>
                        <span>${formatRupiah(currentTotal)}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:4px; color:#666;">
                        <span>Metode</span>
                        <span style="text-transform:uppercase; font-weight:600;">${paymentMethod === 'qris' ? 'QRIS' : 'Tunai'}</span>
                    </div>
                    ${paymentMethod === 'cash' ? `
                    <div style="display:flex; justify-content:space-between; margin-top:4px; color:#666;">
                        <span>Uang Diterima</span>
                        <span>${formatRupiah(cashReceived)}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:4px; font-weight:bold; color:#1a6b3c;">
                        <span>Kembalian</span>
                        <span>${formatRupiah(change)}</span>
                    </div>` : ''}
                </div>
            </div>
        `;

        Swal.fire({
            title: 'Konfirmasi transaksi',
            html: receiptHtml,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1c1410',
            cancelButtonColor: '#57504a',
            confirmButtonText: 'Ya, proses',
            cancelButtonText: 'Batal',
            width: 420,
        }).then((result) => {
            if (result.isConfirmed) {
                // Cegah double-click: disable semua tombol checkout & submit
                const btnSubmit = document.getElementById('btn-submit');
                const btnCheckout = document.getElementById('btn-checkout');
                const posForm = document.getElementById('pos-form');

                // Disable tombol submit di modal checkout
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memproses…
                `;
                btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');

                // Disable tombol checkout di sidebar
                btnCheckout.disabled = true;

                // Submit form
                posForm.submit();
            }
        });
    }
</script>
@endsection