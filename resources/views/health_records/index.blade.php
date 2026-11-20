@extends('layouts.app')

@section('title', 'Health Records')

@section('content')
    <!-- header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-3xl md:text-4xl font-extrabold text-paw-darkest tracking-tight">Health Records</h1>
            <p class="text-xs md:text-sm text-paw-medium mt-1">Catat riwayat vaksinasi, pemeriksaan dokter, dan kondisi kesehatan hewanmu.</p>
        </div>
        <a href="{{ route('health-records.create') }}"
           class="inline-flex items-center gap-2 bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold px-5 py-2.5 rounded-full transition shrink-0 self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Rekam Medis</span>
        </a>
    </div>

    <!-- notifikasi -->
    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-paw-sage/50 border border-paw-medium text-paw-darkest text-xs font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-paw-dark hover:text-paw-darkest">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-7">
        <!-- pil hewan -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('health-records.index', array_filter(['record_type' => request('record_type')])) }}"
               class="inline-flex items-center px-5 py-2.5 rounded-full text-xs font-semibold transition {{ !request('pet_id') ? 'bg-paw-dark text-white shadow-sm' : 'bg-white text-paw-darkest border border-paw-medium/40 hover:border-paw-medium' }}">
                Semua hewan
            </a>

            @foreach($pets as $pet)
                <a href="{{ route('health-records.index', array_filter(['pet_id' => $pet->id, 'record_type' => request('record_type')])) }}"
                   class="inline-flex items-center gap-2 pl-2 pr-4 py-1.5 rounded-full text-xs font-semibold transition {{ request('pet_id') == $pet->id ? 'bg-paw-dark text-white shadow-sm' : 'bg-white text-paw-darkest border border-paw-medium/40 hover:border-paw-medium' }}">
                    <img src="{{ $pet->photo ? asset('storage/' . $pet->photo) : asset('images/catanddog.png') }}"
                         alt="{{ $pet->name }}"
                         class="w-6 h-6 rounded-full object-cover bg-paw-light">
                    <span>{{ $pet->name }}</span>
                </a>
            @endforeach
        </div>

        <!-- dropdown jenis tindakan -->
        <form method="GET" action="{{ route('health-records.index') }}" class="self-start md:self-auto">
            @if(request('pet_id'))
                <input type="hidden" name="pet_id" value="{{ request('pet_id') }}">
            @endif

            <div class="relative">
                <select name="record_type" onchange="this.form.submit()"
                        class="appearance-none pl-5 pr-10 py-2.5 rounded-full bg-white text-paw-darkest border border-paw-medium/40 hover:border-paw-medium text-xs font-semibold focus:outline-none cursor-pointer">
                    <option value="">Semua jenis tindakan</option>
                    @foreach(['Vaksinasi', 'Pemeriksaan Umum', 'Obat Cacing & Kutu', 'Sterilisasi / Operasi', 'Perawatan Gigi', 'Lainnya'] as $type)
                        <option value="{{ $type }}" {{ request('record_type') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="w-4 h-4 text-paw-darkest absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>
        </form>
    </div>

    @if($records->isEmpty())
        <!-- kosong -->
        <div class="bg-paw-cream rounded-2xl border border-paw-medium p-10 text-center">
            <div class="w-12 h-12 rounded-2xl bg-paw-light text-paw-medium flex items-center justify-center mx-auto mb-3">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <h2 class="font-heading text-base font-bold text-paw-darkest">Belum ada rekam medis</h2>
            <p class="text-xs text-paw-medium mt-1 max-w-md mx-auto">Simpan catatan vaksinasi, obat cacing, atau riwayat konsultasi dokter hewan agar mudah dipantau kapan saja.</p>
            <a href="{{ route('health-records.create') }}"
               class="inline-flex items-center gap-2 bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold px-5 py-2.5 rounded-full transition mt-4">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Rekam Medis Pertama</span>
            </a>
        </div>
    @else
        @php
            $groupedRecords = $records->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->record_date)->translatedFormat('F Y');
            });
        @endphp

        <!-- daftar per bulan -->
        <div class="space-y-6">
            @foreach($groupedRecords as $monthYear => $monthRecords)
                <div>
                    <!-- header bulan -->
                    <div class="flex items-baseline gap-2 mb-3">
                        <h2 class="font-heading text-sm font-extrabold text-paw-darkest">
                            {{ $monthYear }}
                        </h2>
                        <span class="text-[11px] font-medium text-paw-medium">
                            {{ $monthRecords->count() }} catatan
                        </span>
                    </div>

                    <!-- daftar kartu -->
                    <div class="space-y-3.5">
                        @foreach($monthRecords as $record)
                            <div class="bg-white rounded-3xl border border-paw-medium p-5 shadow-sm flex items-center gap-4">
                                <!-- foto hewan -->
                                <img src="{{ $record->pet->photo ? asset('storage/' . $record->pet->photo) : asset('images/catanddog.png') }}"
                                     alt="{{ $record->pet->name }}"
                                     class="w-[88px] h-[88px] rounded-2xl object-cover bg-paw-light shrink-0">

                                <!-- isi kartu -->
                                <div class="flex-1 min-w-0">
                                    <!-- baris atas: judul & tombol aksi -->
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2.5">
                                                <h3 class="font-heading text-base md:text-lg font-extrabold text-paw-darkest leading-tight">
                                                    {{ $record->title }}
                                                </h3>
                                                <span class="text-[11px] font-semibold bg-paw-light text-paw-medium px-3 py-0.5 rounded-full">
                                                    {{ $record->record_type }}
                                                </span>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-1.5 text-xs text-paw-medium mt-1">
                                                <span class="font-bold text-paw-darkest">{{ $record->pet->name }}</span>
                                                <span>•</span>
                                                <span>{{ \Carbon\Carbon::parse($record->record_date)->translatedFormat('d M Y') }}</span>
                                                @if($record->vet_name)
                                                    <span>•</span>
                                                    <span>{{ $record->vet_name }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- tombol edit & hapus -->
                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="{{ route('health-records.edit', $record->id) }}"
                                               class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold transition">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                                <span>Edit</span>
                                            </a>

                                            <form action="{{ route('health-records.destroy', $record->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus rekam medis ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-red-600 hover:bg-red-700 text-white text-xs font-semibold transition">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- kotak catatan full width -->
                                    @if($record->diagnosis_or_notes)
                                        <div class="mt-2.5 w-full bg-paw-light/85 rounded-lg px-3.5 py-2 text-xs text-paw-darkest">
                                            {{ $record->diagnosis_or_notes }}
                                        </div>
                                    @endif

                                    <!-- kontrol berikutnya -->
                                    @if($record->next_visit_date)
                                        <p class="text-[11px] font-medium text-paw-medium mt-2">
                                            Kontrol berikutnya: <span class="font-bold text-paw-darkest">{{ \Carbon\Carbon::parse($record->next_visit_date)->translatedFormat('d M Y') }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection