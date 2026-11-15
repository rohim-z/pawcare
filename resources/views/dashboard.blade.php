@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $hariIniTeks = $namaHari[now()->dayOfWeek] . ', ' . now()->day . ' ' . $namaBulan[now()->month] . ' ' . now()->year;
        $bulanTahunTeks = $namaBulan[now()->month] . ' ' . now()->year;
        $bulanSajaTeks = $namaBulan[now()->month];
        $jadwalBelumSelesai = $schedules->where('status', '!=', 'completed')->count();
    @endphp

    <!-- Header Sapaan -->
    <div class="mb-6">
        <h1 class="font-heading text-3xl md:text-4xl font-extrabold text-paw-darkest tracking-tight">
            Halo, {{ explode(' ', Auth::user()->name ?? 'User')[0] }}
        </h1>
        <p class="text-xs md:text-sm text-paw-medium mt-1">
            {{ $hariIniTeks }}. 
            {{ $jadwalBelumSelesai > 0 
                ? 'Ada ' . $jadwalBelumSelesai . ' jadwal yang perlu Anda cek.' 
                : 'Belum ada jadwal perawatan yang perlu dicek.' }}
        </p>
    </div>

    <!-- Profil Hewan Peliharaan -->
    <div class="mb-5">
        @if($pets->isEmpty())
            <div class="bg-paw-cream rounded-2xl border border-paw-medium p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="font-heading text-base font-bold text-paw-darkest">Belum ada hewan peliharaan</h2>
                    <p class="text-xs text-paw-medium mt-0.5">Tambahkan profil kucing atau anjing pertamamu untuk mulai mencatat perawatan.</p>
                </div>
                <a href="#" class="inline-flex items-center gap-2 bg-paw-medium hover:bg-paw-dark text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shrink-0">
                    <span>+ Tambah Hewan</span>
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($pets as $pet)
                    <div class="bg-paw-cream rounded-2xl border border-paw-medium p-4 flex items-center gap-4">
                        <img src="{{ $pet->photo ? asset('storage/' . $pet->photo) : asset('images/catanddog.png') }}" alt="{{ $pet->name }}" class="w-16 h-16 rounded-full object-cover border border-paw-medium bg-paw-light shrink-0">
                        <div>
                            <h2 class="font-heading text-base font-bold text-paw-darkest">{{ $pet->name }}</h2>
                            <p class="text-xs text-paw-medium">{{ $pet->species ?? 'Hewan' }}{{ $pet->breed ? ', ' . $pet->breed : '' }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="text-[11px] font-semibold bg-paw-light text-paw-darkest px-2.5 py-0.5 rounded-full">{{ $pet->weight ?? '-' }} kg</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Kartu Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
        <div class="bg-paw-sage rounded-2xl border border-paw-medium p-4">
            <p class="text-xs font-medium text-paw-darkest">Vaksin berikutnya</p>
            <p class="font-heading text-2xl font-bold text-paw-darkest mt-1">-</p>
            <p class="text-[11px] text-paw-dark mt-1">Belum ada jadwal vaksin</p>
        </div>

        <div class="bg-paw-sage rounded-2xl border border-paw-medium p-4">
            <p class="text-xs font-medium text-paw-darkest">Grooming berikutnya</p>
            <p class="font-heading text-2xl font-bold text-paw-darkest mt-1">-</p>
            <p class="text-[11px] text-paw-dark mt-1">Belum ada jadwal grooming</p>
        </div>

        <div class="bg-paw-sage rounded-2xl border border-paw-medium p-4">
            <p class="text-xs font-medium text-paw-darkest">Jadwal minggu ini</p>
            <p class="font-heading text-2xl font-bold text-paw-darkest mt-1">{{ $schedules->count() }}</p>
            <p class="text-[11px] text-paw-dark mt-1">0 sudah selesai</p>
        </div>

        <div class="bg-paw-sage rounded-2xl border border-paw-medium p-4">
            <p class="text-xs font-medium text-paw-darkest">Pengeluaran {{ $bulanSajaTeks }}</p>
            <p class="font-heading text-2xl font-bold text-paw-darkest mt-1">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</p>
            <p class="text-[11px] text-paw-dark mt-1">Belum ada transaksi</p>
        </div>
    </div>

    <!-- Pengingat Jadwal & Kalender -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
        <div class="bg-paw-cream rounded-2xl border border-paw-medium p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading text-base font-bold text-paw-darkest">Pengingat dan jadwal</h2>
                <span class="text-[11px] text-paw-medium">Centang jika sudah selesai</span>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center text-center py-10 px-4 rounded-xl border border-paw-medium/40 bg-paw-light/40">
                <p class="text-xs font-semibold text-paw-darkest">Belum ada jadwal perawatan</p>
                <p class="text-[11px] text-paw-medium mt-1">Jadwal vaksin, grooming, atau obat akan muncul di sini setelah dibuat.</p>
            </div>
        </div>

        <div class="bg-paw-cream rounded-2xl border border-paw-medium p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading text-base font-bold text-paw-darkest">{{ $bulanTahunTeks }}</h2>
                <span class="text-[11px] text-paw-medium">{{ $schedules->count() }} kegiatan bulan ini</span>
            </div>

            <div class="grid grid-cols-7 text-center text-[11px] font-medium text-paw-medium mb-2">
                <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
            </div>

            @php
                $startOfMonth = now()->startOfMonth();
                $daysInMonth = now()->daysInMonth;
                $startDayOfWeek = $startOfMonth->dayOfWeekIso;
                $today = now()->day;
            @endphp

            <div class="grid grid-cols-7 gap-y-2 text-center text-xs font-medium text-paw-darkest my-auto">
                @for($i = 1; $i < $startDayOfWeek; $i++)
                    <span></span>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @if($day == $today)
                        <div class="py-1 flex items-center justify-center">
                            <span class="w-8 h-8 rounded-lg bg-paw-medium text-white flex items-center justify-center font-bold">{{ $day }}</span>
                        </div>
                    @else
                        <div class="py-1.5 text-paw-darkest">{{ $day }}</div>
                    @endif
                @endfor
            </div>
        </div>
    </div>

    <!-- Grafik Berat Badan & Pengeluaran -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-paw-cream rounded-2xl border border-paw-medium p-5 flex flex-col justify-between">
            <div class="flex items-start justify-between mb-3">
                <h2 class="font-heading text-base font-bold text-paw-darkest">Berat badan</h2>
                <p class="text-[11px] text-paw-medium">Kg, 6 bulan terakhir</p>
            </div>

            <div class="h-48 rounded-xl border border-paw-medium/40 bg-paw-light/40 flex flex-col items-center justify-center text-center p-4">
                <p class="text-xs font-semibold text-paw-darkest">Belum ada riwayat berat badan</p>
                <p class="text-[11px] text-paw-medium mt-1">Grafik pertumbuhan akan tampil setelah kamu mencatat berat badan hewan.</p>
            </div>
        </div>

        <div class="bg-paw-cream rounded-2xl border border-paw-medium p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-heading text-base font-bold text-paw-darkest">Pengeluaran bulan ini</h2>
                    <span class="text-[11px] text-paw-medium">Rp, {{ $bulanSajaTeks }}</span>
                </div>

                <div class="space-y-3.5">
                    @foreach(['Makanan', 'Grooming', 'Dokter hewan', 'Lainnya'] as $kategori)
                        <div>
                            <div class="flex justify-between text-xs font-medium text-paw-darkest mb-1">
                                <span>{{ $kategori }}</span>
                                <span class="font-semibold">Rp 0</span>
                            </div>
                            <div class="w-full h-2 bg-paw-light rounded-full overflow-hidden">
                                <div class="h-full bg-paw-medium rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-paw-medium/30 flex items-center justify-between">
                <span class="text-xs text-paw-medium">Total</span>
                <span class="font-heading text-lg font-extrabold text-paw-darkest">Rp 0</span>
            </div>
        </div>
    </div>
@endsection