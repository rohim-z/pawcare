@extends('layouts.app')

@section('title', 'My Pets')

@section('content')
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-3xl md:text-4xl font-extrabold text-paw-darkest tracking-tight">My Pets</h1>
            <p class="text-xs md:text-sm text-paw-medium mt-1">Kelola profil dan data dasar hewan peliharaanmu.</p>
        </div>
        <a href="{{ route('pets.create') }}" class="inline-flex items-center gap-2 bg-paw-medium hover:bg-paw-dark text-white text-xs font-semibold px-4 py-2.5 rounded-full transition shrink-0 self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Hewan</span>
        </a>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-paw-sage/50 border border-paw-medium text-paw-darkest text-xs font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-paw-dark hover:text-paw-darkest">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if($pets->isEmpty())
        <!-- Tampilan Kosong -->
        <div class="bg-paw-cream rounded-2xl border border-paw-medium p-10 text-center">
            <div class="w-12 h-12 rounded-2xl bg-paw-light text-paw-medium flex items-center justify-center mx-auto mb-3">
                <i data-lucide="paw-print" class="w-6 h-6"></i>
            </div>
            <h2 class="font-heading text-base font-bold text-paw-darkest">Belum ada hewan peliharaan</h2>
            <p class="text-xs text-paw-medium mt-1 max-w-md mx-auto">Tambahkan profil kucing, anjing, atau hewan kesayanganmu untuk mulai mencatat jadwal dan kesehatannya.</p>
            <a href="{{ route('pets.create') }}" class="inline-flex items-center gap-2 bg-paw-medium hover:bg-paw-dark text-white text-xs font-semibold px-4 py-2.5 rounded-full transition mt-4">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Hewan Pertama</span>
            </a>
        </div>
    @else
        <!-- Grid Kartu Hewan -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pets as $pet)
                @php
                    // Hitung umur otomatis
                    $umurTeks = 'Umur belum diisi';
                    if ($pet->birth_date) {
                        $lahir = \Carbon\Carbon::parse($pet->birth_date);
                        $tahun = (int) $lahir->diffInYears(now());
                        $bulan = (int) $lahir->copy()->addYears($tahun)->diffInMonths(now());
                        if ($tahun > 0 && $bulan > 0) {
                            $umurTeks = $tahun . ' tahun ' . $bulan . ' bulan';
                        } elseif ($tahun > 0) {
                            $umurTeks = $tahun . ' tahun';
                        } elseif ($bulan > 0) {
                            $umurTeks = $bulan . ' bulan';
                        } else {
                            $umurTeks = 'Baru lahir';
                        }
                    }

                    // Cek jadwal terlambat & jadwal berikutnya
                    $jadwalTerlambat = $pet->schedules
                        ->where('status', '!=', 'completed')
                        ->where('scheduled_date', '<', now()->toDateString())
                        ->first();

                    $jadwalBerikutnya = $pet->schedules
                        ->where('status', '!=', 'completed')
                        ->where('scheduled_date', '>=', now()->toDateString())
                        ->sortBy('scheduled_date')
                        ->first();

                    $totalPengeluaran = $pet->expenses->sum('amount');
                @endphp

                <div class="bg-white rounded-3xl border border-paw-medium overflow-hidden flex flex-col justify-between shadow-sm">
                    <div>
                        <!-- Foto Penuh di Atas -->
                        <div class="w-full h-56 bg-paw-light overflow-hidden">
                            <img src="{{ $pet->photo ? asset('storage/' . $pet->photo) : asset('images/catanddog.png') }}"
                                 alt="{{ $pet->name }}"
                                 class="w-full h-full object-cover">
                        </div>

                        <!-- Konten Kartu -->
                        <div class="p-5">
                            <h2 class="font-heading text-xl font-extrabold text-paw-darkest leading-tight">{{ $pet->name }}</h2>
                            <p class="text-xs text-paw-medium mt-0.5">
                                {{ $pet->breed ?: $pet->species }} · {{ $umurTeks }}
                            </p>

                            <!-- Pil Status -->
                            <div class="flex flex-wrap gap-2 mt-3">
                                <span class="text-[11px] font-semibold bg-paw-light text-paw-medium px-3 py-1 rounded-full">
                                    {{ $pet->species }}
                                </span>

                                @if($jadwalTerlambat)
                                    <span class="text-[11px] font-semibold bg-red-100 text-red-600 px-3 py-1 rounded-full">
                                        {{ $jadwalTerlambat->title }} terlambat
                                    </span>
                                @else
                                    <span class="text-[11px] font-semibold bg-paw-light text-paw-medium px-3 py-1 rounded-full">
                                        Perawatan aman
                                    </span>
                                @endif
                            </div>

                            <!-- 4 Kotak Ringkasan Hijau -->
                            <div class="grid grid-cols-2 gap-2.5 mt-4">
                                <div class="bg-paw-light rounded-xl px-3.5 py-2.5">
                                    <p class="text-[10px] font-medium text-paw-medium">Berat badan</p>
                                    <p class="font-heading text-xs font-bold text-paw-darkest mt-0.5">
                                        {{ $pet->weight ? $pet->weight . ' kg' : '- kg' }}
                                    </p>
                                </div>

                                <div class="bg-paw-light rounded-xl px-3.5 py-2.5">
                                    <p class="text-[10px] font-medium text-paw-medium">Jenis kelamin</p>
                                    <p class="font-heading text-xs font-bold text-paw-darkest mt-0.5">
                                        {{ $pet->gender }}
                                    </p>
                                </div>

                                <div class="bg-paw-light rounded-xl px-3.5 py-2.5">
                                    <p class="text-[10px] font-medium text-paw-medium">Jadwal terdekat</p>
                                    <p class="font-heading text-xs font-bold text-paw-darkest mt-0.5 truncate">
                                        {{ $jadwalBerikutnya ? \Carbon\Carbon::parse($jadwalBerikutnya->scheduled_date)->format('d M Y') : 'Belum ada' }}
                                    </p>
                                </div>

                                <div class="bg-paw-light rounded-xl px-3.5 py-2.5">
                                    <p class="text-[10px] font-medium text-paw-medium">Total biaya</p>
                                    <p class="font-heading text-xs font-bold text-paw-darkest mt-0.5 truncate">
                                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Detail -->
                    <div class="px-5 pb-5">
                        <a href="{{ route('pets.show', $pet->id) }}"
                           class="block w-full py-2.5 rounded-full bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold text-center transition">
                            Lihat detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection