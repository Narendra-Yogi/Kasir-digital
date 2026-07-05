@extends('layouts.app')

@section('title', 'Buku Kas Harian')
@section('page_title', 'Buku Kas Harian')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    
    {{-- ========== BAGIAN ATAS: FORM PENUTUPAN / STATUS HARI INI ========== --}}
    @if($alreadyClosed)
        {{-- Jika Hari Ini Sudah Tutup Buku --}}
        <div class="bg-gradient-to-r from-emerald-500/10 via-teal-500/5 to-transparent border border-emerald-100 rounded-3xl p-6 lg:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 leading-snug">Buku Kas Hari Ini Telah Ditutup</h3>
                    <p class="text-sm text-gray-500 mt-1">Laporan rekonsiliasi kas telah dikunci dengan sukses.</p>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-emerald-700 font-bold mt-2">
                        <span>Petugas: {{ $alreadyClosed->user->name }}</span>
                        <span class="text-emerald-300">&bull;</span>
                        <span>Waktu: {{ $alreadyClosed->created_at->format('H:i') }} WIB</span>
                        <span class="text-emerald-300">&bull;</span>
                        <span>Tanggal: {{ \Carbon\Carbon::parse($alreadyClosed->date)->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-4 shrink-0 w-full md:w-auto">
                <div class="bg-white/80 border border-emerald-100 rounded-2xl px-5 py-3 text-center flex-1 md:flex-none">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Uang Fisik Aktual</span>
                    <span class="text-base font-black text-emerald-600">Rp {{ number_format($alreadyClosed->actual_cash, 0, ',', '.') }}</span>
                </div>
                <div class="bg-white/80 border border-emerald-100 rounded-2xl px-5 py-3 text-center flex-1 md:flex-none">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Selisih Kas</span>
                    @if($alreadyClosed->discrepancy == 0)
                        <span class="text-base font-black text-emerald-600">Rp 0 (Cocok)</span>
                    @elseif($alreadyClosed->discrepancy > 0)
                        <span class="text-base font-black text-amber-600">+Rp {{ number_format($alreadyClosed->discrepancy, 0, ',', '.') }}</span>
                    @else
                        <span class="text-base font-black text-red-600">-Rp {{ number_format(abs($alreadyClosed->discrepancy), 0, ',', '.') }}</span>
                    @endif
                </div>
                @if(auth()->user()->role === 'admin')
                <button onclick="openEditModal()" class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm transition-colors shadow-md shadow-brand-600/20 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </button>
                @endif
            </div>
        </div>
    @else
        {{-- Jika Hari Ini Belum Tutup Buku --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Bagian Kiri: Ringkasan Sistem Real-time --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm space-y-5">
                    <h3 class="text-base font-black text-gray-900 border-b border-gray-50 pb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                        Ringkasan Sistem Hari Ini
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 rounded-2xl bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center font-bold text-sm shrink-0">Rp</span>
                                <span class="text-xs font-semibold text-gray-500">Penjualan Tunai</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900" id="sys-cash-sales" data-value="{{ $systemCashSales }}">Rp {{ number_format($systemCashSales, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 rounded-2xl bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">QR</span>
                                <span class="text-xs font-semibold text-gray-500">Penjualan QRIS</span>
                            </div>
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($systemQrisSales, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center p-3 rounded-2xl bg-gray-50/50">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                <span class="text-xs font-semibold text-gray-500">Pengeluaran Toko</span>
                            </div>
                            <span class="text-sm font-bold text-red-600" id="sys-expenses" data-value="{{ $systemExpenses }}">-Rp {{ number_format($systemExpenses, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Form Pengisian Tutup Kasir --}}
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl p-6 lg:p-8 border border-gray-100 shadow-sm">
                    <form action="{{ route('buku-kas-harian.store') }}" method="POST" id="closing-form">
                        @csrf
                        <div class="space-y-6">
                            <h3 class="text-base font-black text-gray-900 border-b border-gray-50 pb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Rekonsiliasi Kas & Tutup Buku
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Modal Awal Kasir (Rupiah)</label>
                                    <input type="text" name="starting_cash" id="starting_cash" value="200.000" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors font-bold text-gray-900 text-sm" placeholder="200.000" oninput="formatThousandsInput(this); calculateClosingLive()">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Uang Fisik Laci (Rupiah)</label>
                                    <input type="text" name="actual_cash" id="actual_cash" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors font-bold text-gray-900 text-sm" placeholder="Masukkan uang di laci" required oninput="formatThousandsInput(this); calculateClosingLive()">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Catatan Kejadian Penting Hari Ini</label>
                                <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors text-sm text-gray-800" placeholder="Contoh: Aman lancar, beli tambahan cabai Rp 15.000 dengan uang laci, kembalian kurang Rp 2.000..."></textarea>
                            </div>
                            
                            {{-- Kalkulator Hasil Real-time (JS Live) --}}
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-xs font-bold">
                                <div>
                                    <span class="text-gray-400 block uppercase tracking-wider">Estimasi Kas Sistem</span>
                                    <span class="text-sm font-black text-gray-800" id="display-expected">Rp 200.000</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 block uppercase tracking-wider">Uang Fisik Kasir</span>
                                    <span class="text-sm font-black text-gray-800" id="display-actual">Rp 0</span>
                                </div>
                                <div class="md:text-right">
                                    <span class="text-gray-400 block uppercase tracking-wider">Analisis Selisih</span>
                                    <span class="text-sm font-black text-emerald-600" id="display-discrepancy">Rp 0 (Cocok)</span>
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-4 rounded-xl shadow-lg shadow-brand-700/20 transition-all cursor-pointer text-center text-sm">
                                Simpan & Tutup Buku Hari Ini
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    @endif

    {{-- ===== MODAL EDIT (HANYA ADMIN) ===== --}}
    @if($alreadyClosed && auth()->user()->role === 'admin')
    <div id="edit-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
        
        {{-- Modal Card --}}
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg p-8 space-y-6 scale-95 transition-transform duration-200" id="edit-modal-card">
            
            {{-- Header --}}
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Edit Buku Kas Harian</h3>
                    <p class="text-xs text-gray-400 mt-1">Tanggal: {{ \Carbon\Carbon::parse($alreadyClosed->date)->translatedFormat('d F Y') }}</p>
                </div>
                <button onclick="closeEditModal()" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Ringkasan Data Sistem (readonly) --}}
            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2 text-xs">
                <p class="font-bold text-gray-400 uppercase tracking-wider mb-2">Data Sistem (Tidak Bisa Diubah)</p>
                <div class="flex justify-between">
                    <span class="text-gray-500">Modal Awal</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format($alreadyClosed->starting_cash, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Penjualan Tunai</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format($alreadyClosed->system_cash_sales, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Pengeluaran</span>
                    <span class="font-bold text-red-500">-Rp {{ number_format($alreadyClosed->system_expenses, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-2">
                    <span class="text-gray-500 font-semibold">Estimasi Kas Sistem</span>
                    <span class="font-black text-gray-900">Rp {{ number_format($alreadyClosed->starting_cash + $alreadyClosed->system_cash_sales - $alreadyClosed->system_expenses, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Form Edit --}}
            <form action="{{ route('buku-kas-harian.update', $alreadyClosed->id) }}" method="POST" id="edit-kas-form">
                @csrf
                @method('PUT')
                <div class="space-y-5">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Uang Fisik Laci (Rupiah)</label>
                        <input type="text" name="actual_cash" id="edit-actual-cash"
                            value="{{ number_format($alreadyClosed->actual_cash, 0, ',', '.') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors font-bold text-gray-900 text-sm"
                            oninput="formatThousandsInput(this); updateEditDiscrepancy()" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Catatan</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:border-brand-500 transition-colors text-sm text-gray-800">{{ $alreadyClosed->notes }}</textarea>
                    </div>

                    {{-- Preview Selisih Baru --}}
                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Preview Selisih Baru</span>
                        <span class="text-sm font-black" id="edit-discrepancy-display">Rp 0 (Cocok)</span>
                    </div>

                    <button type="submit" class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-700/20 transition-all cursor-pointer text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    
    {{-- ========== BAGIAN BAWAH: TABEL RIWAYAT TUTUP BUKU ========== --}}
    <div class="space-y-4">
        <h2 class="text-lg font-black text-gray-900">Riwayat Penutupan Kas Harian</h2>
        
        <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[900px]">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kasir</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Modal Awal</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Pemasukan Tunai</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Pengeluaran</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Sisa Saldo Kas</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Fisik Kasir</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Selisih</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-[13px]">
                        @forelse($historyLogs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-gray-700 font-semibold whitespace-nowrap">
                                    {{ $log->user->name }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    Rp {{ number_format($log->starting_cash, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($log->system_cash_sales, 0, ',', '.') }}
                                    <span class="block text-[10px] text-gray-400 font-medium">QRIS: Rp {{ number_format($log->system_qris_sales, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-red-500 whitespace-nowrap">
                                    -Rp {{ number_format($log->system_expenses, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700 whitespace-nowrap">
                                    Rp {{ number_format($log->starting_cash + $log->system_cash_sales - $log->system_expenses, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 font-black text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($log->actual_cash, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->discrepancy == 0)
                                        <span class="px-2.5 py-1 rounded-full bg-green-50 text-green-700 font-bold text-[11px]">Cocok</span>
                                    @elseif($log->discrepancy > 0)
                                        <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 font-bold text-[11px]">Surplus (+Rp {{ number_format($log->discrepancy, 0, ',', '.') }})</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-700 font-bold text-[11px]">Defisit (-Rp {{ number_format(abs($log->discrepancy), 0, ',', '.') }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 max-w-xs truncate" title="{{ $log->notes }}">
                                    {{ $log->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-400 italic">Belum ada riwayat penutupan kas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>

<script>
    function calculateClosingLive() {
        const cashSales = parseFloat(document.getElementById('sys-cash-sales').getAttribute('data-value')) || 0;
        const expenses = parseFloat(document.getElementById('sys-expenses').getAttribute('data-value')) || 0;
        
        const inputStarting = document.getElementById('starting_cash');
        const inputActual = document.getElementById('actual_cash');
        
        const startingVal = parseFloat(inputStarting.value.replace(/\D/g, '')) || 0;
        const actualVal = parseFloat(inputActual.value.replace(/\D/g, '')) || 0;
        
        // expected = Modal Awal + Penjualan Tunai - Pengeluaran
        const expected = startingVal + cashSales - expenses;
        const discrepancy = actualVal - expected;
        
        // Update display text
        document.getElementById('display-expected').innerText = formatRupiahJS(expected);
        document.getElementById('display-actual').innerText = formatRupiahJS(actualVal);
        
        const displayDisc = document.getElementById('display-discrepancy');
        if (discrepancy === 0) {
            displayDisc.innerText = 'Rp 0 (Cocok)';
            displayDisc.className = 'text-sm font-black text-emerald-600';
        } else if (discrepancy > 0) {
            displayDisc.innerText = '+ ' + formatRupiahJS(discrepancy) + ' (Surplus)';
            displayDisc.className = 'text-sm font-black text-amber-600';
        } else {
            displayDisc.innerText = '- ' + formatRupiahJS(Math.abs(discrepancy)) + ' (Defisit)';
            displayDisc.className = 'text-sm font-black text-red-600';
        }
    }

    function formatRupiahJS(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(number);
    }
    
    // Jalankan kalkulator pertama kali untuk inisialisasi visual modal awal
    window.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('closing-form')) {
            calculateClosingLive();
        }
        // Inisialisasi preview selisih di modal edit
        if (document.getElementById('edit-actual-cash')) {
            updateEditDiscrepancy();
        }
    });

    // ---- FUNGSI MODAL EDIT ----
    function openEditModal() {
        const modal = document.getElementById('edit-modal');
        const card  = document.getElementById('edit-modal-card');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        const modal = document.getElementById('edit-modal');
        const card  = document.getElementById('edit-modal-card');
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.classList.remove('opacity-100');
        card.classList.add('scale-95');
        card.classList.remove('scale-100');
        document.body.style.overflow = '';
    }

    // Kalkulasi live selisih di modal edit
    function updateEditDiscrepancy() {
        const expectedCash = {{ $alreadyClosed ? ($alreadyClosed->starting_cash + $alreadyClosed->system_cash_sales - $alreadyClosed->system_expenses) : 0 }};
        const inputEl = document.getElementById('edit-actual-cash');
        if (!inputEl) return;

        const actualVal = parseFloat(inputEl.value.replace(/\D/g, '')) || 0;
        const discrepancy = actualVal - expectedCash;

        const displayEl = document.getElementById('edit-discrepancy-display');
        if (!displayEl) return;

        if (discrepancy === 0) {
            displayEl.innerText = 'Rp 0 (Cocok)';
            displayEl.className = 'text-sm font-black text-emerald-600';
        } else if (discrepancy > 0) {
            displayEl.innerText = '+ ' + formatRupiahJS(discrepancy) + ' (Surplus)';
            displayEl.className = 'text-sm font-black text-amber-600';
        } else {
            displayEl.innerText = '- ' + formatRupiahJS(Math.abs(discrepancy)) + ' (Defisit)';
            displayEl.className = 'text-sm font-black text-red-600';
        }
    }
</script>
@endsection
