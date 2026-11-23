@extends('layouts.app')

@section('title', 'Care Schedule')

@section('content')
    <!-- header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="font-heading text-3xl md:text-4xl font-extrabold text-paw-darkest tracking-tight">Care Schedule</h1>
            <p class="text-xs md:text-sm text-paw-medium mt-1">Atur jadwal mandi, grooming, vitamin, dan rutinitas perawatan hewanmu.</p>
        </div>
        <a href="{{ route('care-schedules.create') }}"
           class="inline-flex items-center gap-2 bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold px-5 py-2.5 rounded-full transition shrink-0 self-start sm:self-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Jadwal</span>
        </a>
    </div>

    <!-- notifikasi -->
    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-paw-sage/50 border border-paw-medium text-paw-darkest text-xs font-medium flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-paw-dark hover:text-paw-darkest">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-7">
        <!-- pil hewan -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('care-schedules.index', array_filter(['status' => $filterStatus !== 'all' ? $filterStatus : null])) }}"
               class="inline-flex items-center px-5 py-2.5 rounded-full text-xs font-semibold transition {{ !request('pet_id') ? 'bg-paw-dark text-white shadow-sm' : 'bg-white text-paw-darkest border border-paw-medium/40 hover:border-paw-medium' }}">
                Semua hewan
            </a>

            @foreach($pets as $pet)
                <a href="{{ route('care-schedules.index', array_filter(['pet_id' => $pet->id, 'status' => $filterStatus !== 'all' ? $filterStatus : null])) }}"
                   class="inline-flex items-center gap-2 pl-2 pr-4 py-1.5 rounded-full text-xs font-semibold transition {{ request('pet_id') == $pet->id ? 'bg-paw-dark text-white shadow-sm' : 'bg-white text-paw-darkest border border-paw-medium/40 hover:border-paw-medium' }}">
                    <img src="{{ $pet->photo ? asset('storage/' . $pet->photo) : asset('images/catanddog.png') }}"
                         alt="{{ $pet->name }}"
                         class="w-6 h-6 rounded-full object-cover bg-paw-light">
                    <span>{{ $pet->name }}</span>
                </a>
            @endforeach
        </div>

        <!-- pil status -->
        <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
            @php
                $statuses = [
                    'all' => 'Semua',
                    'upcoming' => 'Mendatang',
                    'overdue' => 'Terlewat',
                    'completed' => 'Selesai',
                ];
            @endphp
            @foreach($statuses as $key => $label)
                <a href="{{ route('care-schedules.index', array_filter(['pet_id' => request('pet_id'), 'status' => $key !== 'all' ? $key : null])) }}"
                   class="inline-flex items-center px-4 py-2 rounded-full text-xs font-semibold transition {{ $filterStatus === $key ? 'bg-paw-dark text-white shadow-sm' : 'bg-white text-paw-darkest border border-paw-medium/40 hover:border-paw-medium' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    @if($schedules->isEmpty())
        <!-- kosong -->
        <div class="bg-paw-cream rounded-2xl border border-paw-medium p-10 text-center">
            <div class="w-12 h-12 rounded-2xl bg-paw-light text-paw-medium flex items-center justify-center mx-auto mb-3">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <h2 class="font-heading text-base font-bold text-paw-darkest">Belum ada jadwal perawatan</h2>
            <p class="text-xs text-paw-medium mt-1 max-w-md mx-auto">Buat jadwal mandi, grooming, pemberian vitamin, atau kontrol rutin agar tidak ada perawatan yang terlewat.</p>
            <a href="{{ route('care-schedules.create') }}"
               class="inline-flex items-center gap-2 bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold px-5 py-2.5 rounded-full transition mt-4">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Jadwal Pertama</span>
            </a>
        </div>
    @else
        @php
            $groupedSchedules = $schedules->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->scheduled_date)->translatedFormat('F Y');
            });
        @endphp

        <!-- daftar per bulan -->
        <div class="space-y-6">
            @foreach($groupedSchedules as $monthYear => $monthSchedules)
                <div>
                    <!-- header bulan -->
                    <div class="flex items-baseline gap-2 mb-3">
                        <h2 class="font-heading text-sm font-extrabold text-paw-darkest">
                            {{ $monthYear }}
                        </h2>
                        <span class="text-[11px] font-medium text-paw-medium">
                            {{ $monthSchedules->count() }} jadwal
                        </span>
                    </div>

                    <!-- daftar kartu -->
                    <div class="space-y-3.5">
                        @foreach($monthSchedules as $schedule)
                            @php
                                $isCompleted = $schedule->status === 'completed';
                                $isOverdue = !$isCompleted && \Carbon\Carbon::parse($schedule->scheduled_date)->lt(now()->startOfDay());
                            @endphp

                            <div class="bg-white rounded-3xl border {{ $isOverdue ? 'border-red-400' : 'border-paw-medium' }} p-5 shadow-sm flex items-center gap-4 transition {{ $isCompleted ? 'opacity-70' : '' }}">
                                <!-- tombol centang selesai -->
                                <form action="{{ route('care-schedules.toggle', $schedule->id) }}" method="POST" class="shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="{{ $isCompleted ? 'Tandai belum selesai' : 'Tandai selesai' }}"
                                            class="w-7 h-7 rounded-full border-2 flex items-center justify-center transition {{ $isCompleted ? 'bg-paw-dark border-paw-dark text-white' : 'bg-white border-paw-medium hover:border-paw-darkest text-transparent hover:text-paw-medium' }}">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>

                                <!-- foto hewan -->
                                <img src="{{ $schedule->pet->photo ? asset('storage/' . $schedule->pet->photo) : asset('images/catanddog.png') }}"
                                     alt="{{ $schedule->pet->name }}"
                                     class="w-[88px] h-[88px] rounded-2xl object-cover bg-paw-light shrink-0 hidden sm:block">

                                <!-- isi kartu -->
                                <div class="flex-1 min-w-0">
                                    <!-- baris atas: judul & tombol aksi -->
                                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2.5">
                                                <h3 class="font-heading text-base md:text-lg font-extrabold text-paw-darkest leading-tight {{ $isCompleted ? 'line-through text-paw-medium' : '' }}">
                                                    {{ $schedule->title }}
                                                </h3>
                                                <span class="text-[11px] font-semibold bg-paw-light text-paw-medium px-3 py-0.5 rounded-full">
                                                    {{ $schedule->category }}
                                                </span>
                                                @if($isCompleted)
                                                    <span class="text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-0.5 rounded-full">
                                                        Selesai
                                                    </span>
                                                @elseif($isOverdue)
                                                    <span class="text-[11px] font-semibold bg-red-50 text-red-600 border border-red-200 px-3 py-0.5 rounded-full">
                                                        Terlewat
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="flex flex-wrap items-center gap-1.5 text-xs text-paw-medium mt-1">
                                                <span class="font-bold text-paw-darkest">{{ $schedule->pet->name }}</span>
                                                <span>•</span>
                                                <span>{{ \Carbon\Carbon::parse($schedule->scheduled_date)->translatedFormat('d M Y') }}</span>
                                                @if($schedule->scheduled_time)
                                                    <span>•</span>
                                                    <span>{{ \Carbon\Carbon::parse($schedule->scheduled_time)->format('H:i') }} WIB</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- tombol edit & hapus -->
                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="{{ route('care-schedules.edit', $schedule->id) }}"
                                               class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold transition">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                                <span>Edit</span>
                                            </a>

                                            <form action="{{ route('care-schedules.destroy', $schedule->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus jadwal perawatan ini?')">
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
                                    @if($schedule->notes)
                                        <div class="mt-2.5 w-full bg-paw-light/85 rounded-lg px-3.5 py-2 text-xs text-paw-darkest">
                                            {{ $schedule->notes }}
                                        </div>
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