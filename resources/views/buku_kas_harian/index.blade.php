@extends('layouts.app')

@section('title', 'Buku Kas Harian')
@section('page_title', 'Buku Kas Harian')

@section('content')
<div class="space-y-6 rise">

    {{-- ========== BAGIAN ATAS: FORM PENUTUPAN / STATUS HARI INI ========== --}}
    @if($alreadyClosed)
        {{-- Jika Hari Ini Sudah Tutup Buku --}}
        <div class="card card-pad flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#1c1410] text-[#f7f1e6] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-display text-lg font-semibold tracking-tight">Buku kas hari ini telah ditutup</h3>
                    <p class="text-[13px] text-[#8a8179] mt-0.5">Rekonsiliasi kas dikunci · Petugas {{ $alreadyClosed->user->name }} · {{ $alreadyClosed->created_at->format('H:i') }} WIB · {{ \Carbon\Carbon::parse($alreadyClosed->date)->translatedFormat('d F Y') }}</p>
                </div>
            </div>
            <div class="flex gap-4 shrink-0 w-full md:w-auto items-center">
                <div class="px-4 py-2.5 text-center flex-1 md:flex-none border-l border-[#e8e0d3]">
                    <span class="block text-xs text-[#8a8179]">Uang fisik aktual</span>
                    <span class="font-display text-lg font-semibold tnum">Rp {{ number_format($alreadyClosed->actual_cash, 0, ',', '.') }}</span>
                </div>
                <div class="px-4 py-2.5 text-center flex-1 md:flex-none border-l border-[#e8e0d3]">
                    <span class="block text-xs text-[#8a8179]">Selisih kas</span>
                    @if($alreadyClosed->discrepancy == 0)
                        <span class="font-display text-lg font-semibold">Rp 0</span>
                    @elseif($alreadyClosed->discrepancy > 0)
                        <span class="font-display text-lg font-semibold text-[#7a5c14] tnum">+Rp {{ number_format($alreadyClosed->discrepancy, 0, ',', '.') }}</span>
                    @else
                        <span class="font-display text-lg font-semibold text-[#931b13] tnum">−Rp {{ number_format(abs($alreadyClosed->discrepancy), 0, ',', '.') }}</span>
                    @endif
                </div>
                @if(auth()->user()->role === 'admin')
                <button onclick="openEditModal()" class="btn-outline !text-[13px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </button>
                @endif
            </div>
        </div>
    @else
        {{-- Jika Hari Ini Belum Tutup Buku --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            {{-- Bagian Kiri: Ringkasan Sistem Real-time --}}
            <div class="lg:col-span-5">
                <div class="card card-pad">
                    <h3 class="font-display text-[17px] font-semibold tracking-tight pb-3 mb-4 border-b border-[#f1ebe0]">Ringkasan sistem hari ini</h3>

                    <div class="divide-y divide-[#f1ebe0]">
                        <div class="flex justify-between items-center py-3">
                            <span class="text-[13px] text-[#57504a]">Penjualan tunai</span>
                            <span class="text-sm font-semibold tnum" id="sys-cash-sales" data-value="{{ $systemCashSales }}">Rp {{ number_format($systemCashSales, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3">
                            <span class="text-[13px] text-[#57504a]">Penjualan QRIS</span>
                            <span class="text-sm font-semibold tnum">Rp {{ number_format($systemQrisSales, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center py-3">
                            <span class="text-[13px] text-[#57504a]">Pengeluaran toko</span>
                            <span class="text-sm font-semibold tnum" id="sys-expenses" data-value="{{ $systemExpenses }}">−Rp {{ number_format($systemExpenses, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Form Pengisian Tutup Kasir --}}
            <div class="lg:col-span-7">
                <div class="card card-pad">
                    <form action="{{ route('buku-kas-harian.store') }}" method="POST" id="closing-form">
                        @csrf
                        <div class="space-y-5">
                            <h3 class="font-display text-[17px] font-semibold tracking-tight pb-3 border-b border-[#f1ebe0]">
                                Rekonsiliasi kas & tutup buku
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[13px] text-[#57504a] mb-1.5">Modal awal laci / uang awal (Rp)</label>
                                    <div class="relative">
                                        <input type="text" name="starting_cash" id="starting_cash" value="0" class="field font-semibold tnum" placeholder="Contoh: 100.000" required oninput="formatThousandsInput(this); calculateClosingLive()">
                                    </div>
                                    <p class="text-xs text-[#8a8179] mt-1.5">Isi manual: uang yang ada di laci sebelum buka. Rumus: awal + tunai − pengeluaran = seharusnya di laci.</p>
                                </div>

                                <div>
                                    <label class="block text-[13px] text-[#57504a] mb-1.5">Uang fisik di laci (Rp)</label>
                                    <input type="text" name="actual_cash" id="actual_cash" class="field font-semibold tnum" placeholder="Hitung uang di laci" required oninput="formatThousandsInput(this); calculateClosingLive()">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[13px] text-[#57504a] mb-1.5">Catatan hari ini</label>
                                <textarea name="notes" rows="3" class="field" placeholder="Contoh: ramai lancar, beli cabai Rp 15.000 dari laci…"></textarea>
                            </div>

                            {{-- Kalkulator Hasil Real-time (JS Live) --}}
                            <div class="px-4 py-3.5 rounded-[10px] bg-[#f7f2e9] border border-[#e8e0d3] flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                                <div>
                                    <span class="text-xs text-[#8a8179] block">Estimasi kas sistem</span>
                                    <span class="font-display text-[17px] font-semibold tnum" id="display-expected">Rp 0</span>
                                </div>
                                <div>
                                    <span class="text-xs text-[#8a8179] block">Uang fisik kasir</span>
                                    <span class="font-display text-[17px] font-semibold tnum" id="display-actual">Rp 0</span>
                                </div>
                                <div class="md:text-right">
                                    <span class="text-xs text-[#8a8179] block">Selisih</span>
                                    <span class="font-display text-[17px] font-semibold" id="display-discrepancy">Rp 0</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-ink w-full py-3.5 text-sm">
                                Simpan & tutup buku hari ini
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
        <div class="relative bg-[#fffdf9] rounded-[16px] border border-[#e8e0d3] w-full max-w-lg p-7 space-y-5 scale-95 transition-transform duration-200" id="edit-modal-card">

            {{-- Header --}}
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-display text-lg font-semibold tracking-tight">Edit buku kas harian</h3>
                    <p class="text-xs text-[#8a8179] mt-1">Tanggal: {{ \Carbon\Carbon::parse($alreadyClosed->date)->translatedFormat('d F Y') }}</p>
                </div>
                <button onclick="closeEditModal()" class="icon-btn" aria-label="Tutup">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Ringkasan Data Sistem (readonly) --}}
            <div class="px-4 py-3.5 rounded-[10px] bg-[#f7f2e9] border border-[#e8e0d3] space-y-2 text-[13px]">
                <p class="text-xs text-[#8a8179] mb-1">Data sistem (tidak bisa diubah)</p>
                <div class="flex justify-between">
                    <span class="text-[#57504a]">Modal awal</span>
                    <span class="font-semibold tnum">Rp {{ number_format($alreadyClosed->starting_cash, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#57504a]">Penjualan tunai</span>
                    <span class="font-semibold tnum">Rp {{ number_format($alreadyClosed->system_cash_sales, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#57504a]">Pengeluaran</span>
                    <span class="font-semibold tnum">−Rp {{ number_format($alreadyClosed->system_expenses, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-[#e8e0d3] pt-2">
                    <span class="font-medium">Estimasi kas sistem</span>
                    <span class="font-semibold tnum">Rp {{ number_format($alreadyClosed->starting_cash + $alreadyClosed->system_cash_sales - $alreadyClosed->system_expenses, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Form Edit --}}
            <form action="{{ route('buku-kas-harian.update', $alreadyClosed->id) }}" method="POST" id="edit-kas-form">
                @csrf
                @method('PUT')
                <div class="space-y-4">

                    <div>
                        <label class="block text-[13px] text-[#57504a] mb-1.5">Uang fisik di laci (Rp)</label>
                        <input type="text" name="actual_cash" id="edit-actual-cash"
                            value="{{ number_format($alreadyClosed->actual_cash, 0, ',', '.') }}"
                            class="field font-semibold tnum"
                            oninput="formatThousandsInput(this); updateEditDiscrepancy()" required>
                    </div>

                    <div>
                        <label class="block text-[13px] text-[#57504a] mb-1.5">Catatan</label>
                        <textarea name="notes" rows="3" class="field">{{ $alreadyClosed->notes }}</textarea>
                    </div>

                    {{-- Preview Selisih Baru --}}
                    <div class="px-4 py-3 rounded-[10px] bg-[#f7f2e9] border border-[#e8e0d3] flex justify-between items-center">
                        <span class="text-xs text-[#8a8179]">Preview selisih baru</span>
                        <span class="font-display text-[17px] font-semibold" id="edit-discrepancy-display">Rp 0</span>
                    </div>

                    <button type="submit" class="btn-ink w-full py-3 text-sm">
                        Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
    
    {{-- ========== BAGIAN BAWAH: TABEL RIWAYAT TUTUP BUKU ========== --}}
    <div class="space-y-3">
        <h2 class="font-display text-lg font-semibold tracking-tight">Riwayat penutupan kas</h2>

        <div class="table-shell bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[900px]">
                    <thead>
                        <tr>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Tanggal</th>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Kasir</th>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Modal awal</th>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Pemasukan tunai</th>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Pengeluaran</th>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Sisa saldo kas</th>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Fisik kasir</th>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Selisih</th>
                            <th class="px-6 py-3.5 text-[11px] font-semibold text-[#7a7268]">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f1ebe0] text-[13px]">
                        @forelse($historyLogs as $log)
                            <tr class="table-row-hover">
                                <td class="px-6 py-3.5 font-semibold whitespace-nowrap tnum">
                                    {{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-3.5 font-medium whitespace-nowrap">
                                    {{ $log->user->name }}
                                </td>
                                <td class="px-6 py-3.5 text-[#57504a] whitespace-nowrap tnum">
                                    Rp {{ number_format($log->starting_cash, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 font-medium whitespace-nowrap tnum">
                                    Rp {{ number_format($log->system_cash_sales, 0, ',', '.') }}
                                    <span class="block text-[11px] text-[#8a8179] font-normal">QRIS: Rp {{ number_format($log->system_qris_sales, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap tnum">
                                    −Rp {{ number_format($log->system_expenses, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 font-medium whitespace-nowrap tnum">
                                    Rp {{ number_format($log->starting_cash + $log->system_cash_sales - $log->system_expenses, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 font-semibold whitespace-nowrap tnum">
                                    Rp {{ number_format($log->actual_cash, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if($log->discrepancy == 0)
                                        <span class="pill pill-success">Cocok</span>
                                    @elseif($log->discrepancy > 0)
                                        <span class="pill pill-warn">Surplus +Rp {{ number_format($log->discrepancy, 0, ',', '.') }}</span>
                                    @else
                                        <span class="pill pill-danger">Defisit −Rp {{ number_format(abs($log->discrepancy), 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-[#8a8179] max-w-xs truncate" title="{{ $log->notes }}">
                                    {{ $log->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-[#8a8179] text-sm">Belum ada riwayat penutupan kas.</td>
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
