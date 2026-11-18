@extends('layouts.app')

@section('title', $pet->name)

@section('content')
    @php
        // Hitung umur otomatis dari birth_date
        $ageText = 'Umur belum diisi';
        if ($pet->birth_date) {
            $diff = \Carbon\Carbon::parse($pet->birth_date)->diff(now());
            if ($diff->y > 0) {
                $ageText = $diff->y . ' tahun' . ($diff->m > 0 ? ' ' . $diff->m . ' bulan' : '');
            } elseif ($diff->m > 0) {
                $ageText = $diff->m . ' bulan';
            } else {
                $ageText = $diff->d . ' hari';
            }
        }

        // Jadwal terdekat yang belum selesai
        $nextSchedule = $pet->schedules
            ->where('status', '!=', 'completed')
            ->sortBy('scheduled_date')
            ->first();
    @endphp

    <!-- Link Kembali -->
    <a href="{{ route('pets.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-paw-medium hover:text-paw-darkest mb-4">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke My Pets</span>
    </a>

    <!-- 1. Kartu Profil Utama -->
    <div class="bg-white rounded-2xl border border-paw-medium p-5 md:p-6 mb-5">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5 md:gap-6">
                <img src="{{ $pet->photo ? asset('storage/' . $pet->photo) : asset('images/catanddog.png') }}"
                     alt="{{ $pet->name }}"
                     class="w-36 h-36 md:w-40 md:h-40 rounded-2xl object-cover border border-paw-medium bg-paw-light shrink-0">

                <div>
                    <h1 class="font-heading text-2xl md:text-3xl font-extrabold text-paw-darkest leading-tight">{{ $pet->name }}</h1>
                    <p class="text-xs md:text-sm text-paw-medium mt-0.5">
                        {{ $pet->species }}{{ $pet->breed ? ' ' . strtolower($pet->breed) : '' }}, {{ $ageText }}
                    </p>

                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="text-xs font-bold bg-paw-light text-paw-darkest px-3 py-1 rounded-full">
                            {{ $pet->weight ? number_format($pet->weight, 1, ',', '.') . ' kg' : '- kg' }}
                        </span>
                        <span class="text-xs font-bold bg-paw-light text-paw-darkest px-3 py-1 rounded-full">
                            {{ $pet->gender }}
                        </span>
                        <span class="text-xs font-bold bg-paw-light text-paw-darkest px-3 py-1 rounded-full">
                            {{ $pet->healthRecords->count() > 0 ? 'Vaksin / medis tercatat' : 'Belum ada catatan medis' }}
                        </span>
                    </div>

                    <!-- Catatan Khusus / Alergi -->
                    <div class="mt-4">
                        <p class="text-xs font-bold text-paw-darkest">Catatan khusus / alergi</p>
                        <p class="text-xs text-paw-medium mt-0.5">
                            {{ $pet->notes ?: 'Tidak ada catatan khusus atau alergi.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tombol Edit & Hapus -->
            <div class="flex items-center gap-2 self-start shrink-0">
                <a href="{{ route('pets.edit', $pet->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold transition">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    <span>Edit profil</span>
                </a>

                <form action="{{ route('pets.destroy', $pet->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data {{ $pet->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-red-600 hover:bg-red-700 text-white text-xs font-semibold transition">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. Tiga Kartu Ringkasan Hijau Sage -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-paw-sage rounded-2xl border border-paw-medium p-5">
            <p class="text-xs font-medium text-paw-darkest">Jadwal berikutnya</p>
            <p class="font-heading text-2xl font-extrabold text-paw-darkest mt-1">
                {{ $nextSchedule ? \Carbon\Carbon::parse($nextSchedule->scheduled_date)->translatedFormat('d M') : 'Belum ada' }}
            </p>
            <p class="text-[11px] text-paw-dark mt-1 truncate">
                {{ $nextSchedule ? $pet->name . ', ' . strtolower($nextSchedule->title) : 'Tidak ada jadwal mendatang' }}
            </p>
        </div>

        <div class="bg-paw-sage rounded-2xl border border-paw-medium p-5">
            <p class="text-xs font-medium text-paw-darkest">Riwayat medis</p>
            <p class="font-heading text-2xl font-extrabold text-paw-darkest mt-1">
                {{ $pet->healthRecords->count() }} Catatan
            </p>
            <p class="text-[11px] text-paw-dark mt-1">
                Pemeriksaan dokter & vaksinasi {{ $pet->name }}
            </p>
        </div>

        <div class="bg-paw-sage rounded-2xl border border-paw-medium p-5">
            <p class="text-xs font-medium text-paw-darkest">Total pengeluaran</p>
            <p class="font-heading text-2xl font-extrabold text-paw-darkest mt-1">
                Rp {{ number_format($pet->expenses->sum('amount'), 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-paw-dark mt-1">
                Akumulasi biaya perawatan {{ $pet->name }}
            </p>
        </div>
    </div>

    <!-- 3. Baris Tengah: Jadwal Perawatan Terbaru & Riwayat Medis Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
        <!-- Jadwal Perawatan Terbaru -->
        <div class="bg-white rounded-2xl border border-paw-medium p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading text-base font-bold text-paw-darkest">Jadwal perawatan terbaru</h2>
                <span class="text-[11px] font-medium text-paw-medium">{{ $pet->schedules->count() }} jadwal</span>
            </div>

            @if($pet->schedules->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center text-center py-8 bg-paw-light/40 rounded-xl">
                    <p class="text-xs font-semibold text-paw-darkest">Belum ada jadwal perawatan</p>
                    <p class="text-[11px] text-paw-medium mt-0.5">Jadwal vaksin dan grooming akan muncul di sini.</p>
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach($pet->schedules->sortByDesc('scheduled_date')->take(4) as $schedule)
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-paw-light/65">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-paw-sage flex items-center justify-center text-paw-darkest shrink-0">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-paw-darkest truncate">{{ $schedule->title }}</p>
                                    <p class="text-[11px] text-paw-medium">{{ \Carbon\Carbon::parse($schedule->scheduled_date)->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold px-3 py-1 rounded-full shrink-0 ml-2 {{ $schedule->status === 'completed' ? 'bg-paw-sage text-paw-darkest' : 'bg-white text-paw-darkest border border-paw-medium/30' }}">
                                {{ $schedule->status === 'completed' ? 'Selesai' : 'Terjadwal' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Riwayat Medis Terbaru -->
        <div class="bg-white rounded-2xl border border-paw-medium p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading text-base font-bold text-paw-darkest">Riwayat medis terbaru</h2>
                <span class="text-[11px] font-medium text-paw-medium">{{ $pet->healthRecords->count() }} catatan</span>
            </div>

            @if($pet->healthRecords->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center text-center py-8 bg-paw-light/40 rounded-xl">
                    <p class="text-xs font-semibold text-paw-darkest">Belum ada riwayat medis</p>
                    <p class="text-[11px] text-paw-medium mt-0.5">Catatan vaksin, obat cacing, dan pemeriksaan akan tampil di sini.</p>
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach($pet->healthRecords->sortByDesc('id')->take(4) as $record)
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-paw-light/65">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-paw-sage flex items-center justify-center text-paw-darkest shrink-0">
                                    <i data-lucide="activity" class="w-4 h-4"></i>
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-paw-darkest truncate">{{ $record->diagnosis ?? $record->title ?? 'Pemeriksaan' }}</p>
                                    <p class="text-[11px] text-paw-medium truncate">{{ $record->notes ?? 'Catatan pemeriksaan rutin' }}</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-semibold text-paw-darkest shrink-0 ml-2">
                                {{ \Carbon\Carbon::parse($record->checkup_date ?? $record->created_at)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- 4. Baris Bawah: Log Berat Badan (Placeholder) & Pengeluaran Terakhir -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Container Log Berat Badan -->
        <div class="bg-white rounded-2xl border border-paw-medium p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading text-base font-bold text-paw-darkest">Log berat badan</h2>
                <span class="text-[11px] font-medium text-paw-medium">{{ $pet->weightLogs->count() }} catatan terakhir</span>
            </div>

            <div class="flex-1 flex flex-col items-center justify-center text-center py-8 bg-paw-light/40 rounded-xl">
                <p class="text-xs font-semibold text-paw-darkest">Belum ada log berat badan</p>
                <p class="text-[11px] text-paw-medium mt-0.5">Grafik dan riwayat berat badan akan tampil setelah fitur Growth aktif.</p>
            </div>
        </div>

        <!-- Pengeluaran Terakhir -->
        <div class="bg-white rounded-2xl border border-paw-medium p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading text-base font-bold text-paw-darkest">Pengeluaran terakhir</h2>
                <span class="text-[11px] font-medium text-paw-medium">{{ $pet->expenses->count() }} transaksi</span>
            </div>

            @if($pet->expenses->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center text-center py-8 bg-paw-light/40 rounded-xl">
                    <p class="text-xs font-semibold text-paw-darkest">Belum ada pengeluaran</p>
                    <p class="text-[11px] text-paw-medium mt-0.5">Catatan pembelian pakan, pasir, dan klinik akan muncul di sini.</p>
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach($pet->expenses->sortByDesc('id')->take(5) as $expense)
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-paw-light/65">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-paw-sage flex items-center justify-center text-paw-darkest shrink-0">
                                    <i data-lucide="wallet" class="w-4 h-4"></i>
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-paw-darkest truncate">{{ $expense->title ?? 'Pengeluaran' }}</p>
                                    <p class="text-[11px] text-paw-medium">
                                        {{ $expense->category ?? 'Umum' }} • {{ \Carbon\Carbon::parse($expense->expense_date ?? $expense->created_at)->translatedFormat('d M') }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-paw-darkest shrink-0 ml-2">
                                Rp {{ number_format($expense->amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection