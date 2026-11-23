@extends('layouts.app')

@section('title', 'Tambah Jadwal Perawatan')

@section('content')
    <!-- Link Kembali -->
    <a href="{{ route('care-schedules.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-paw-medium hover:text-paw-darkest mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke Care Schedule</span>
    </a>

    <!-- Header Halaman -->
    <div class="mb-7">
        <h1 class="font-heading text-2xl md:text-3xl font-extrabold text-paw-darkest">Tambah jadwal perawatan</h1>
        <p class="text-xs md:text-sm text-paw-medium mt-1">Buat pengingat mandi, grooming, pemberian vitamin, atau rutinitas hewanmu.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-300 text-red-700 text-xs">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $selectedPetId = old('pet_id', request('pet_id', $pets->first()?->id));
        $initialPet = $pets->firstWhere('id', $selectedPetId) ?? $pets->first();
        $categories = ['Grooming & Mandi', 'Vitamin & Suplemen', 'Obat Rutin', 'Kontrol Dokter', 'Vaksinasi', 'Lainnya'];
        $selectedCategory = old('category', 'Grooming & Mandi');
    @endphp

    <form action="{{ route('care-schedules.store') }}" method="POST">
        @csrf

        <div class="flex flex-col lg:flex-row items-start gap-8">
            <div class="w-full sm:w-64 shrink-0">
                <div class="aspect-square w-full rounded-3xl border border-paw-medium bg-paw-sage/75 overflow-hidden flex items-center justify-center">
                    <img id="preview-pet-photo"
                         src="{{ $initialPet && $initialPet->photo ? asset('storage/' . $initialPet->photo) : asset('images/catanddog.png') }}"
                         alt="Preview Hewan"
                         class="w-full h-full object-cover">
                </div>

                <h2 id="preview-pet-name" class="font-heading text-xl font-extrabold text-paw-darkest mt-4">
                    {{ $initialPet?->name ?? 'Pilih hewan' }}
                </h2>
                <p id="preview-pet-meta" class="text-xs font-medium text-paw-medium mt-0.5">
                    {{ $initialPet ? $initialPet->species . ($initialPet->breed ? ' • ' . $initialPet->breed : '') : 'Belum ada hewan dipilih' }}
                </p>
            </div>

            <div class="flex-1 w-full space-y-5">
                <div>
                    <label class="block text-xs font-bold text-paw-darkest mb-2">
                        Hewan peliharaan <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($pets as $pet)
                            @php
                                $photoUrl = $pet->photo ? asset('storage/' . $pet->photo) : asset('images/catanddog.png');
                                $metaText = $pet->species . ($pet->breed ? ' • ' . $pet->breed : '');
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio"
                                       name="pet_id"
                                       value="{{ $pet->id }}"
                                       data-name="{{ $pet->name }}"
                                       data-meta="{{ $metaText }}"
                                       data-photo="{{ $photoUrl }}"
                                       class="sr-only peer pet-radio"
                                       {{ $selectedPetId == $pet->id ? 'checked' : '' }}
                                       required>
                                <span class="inline-flex items-center gap-2 pl-2 pr-4 py-1.5 rounded-full text-xs font-semibold bg-white text-paw-darkest border border-paw-medium/30 peer-checked:bg-paw-dark peer-checked:text-white peer-checked:border-paw-dark transition">
                                    <img src="{{ $photoUrl }}" alt="{{ $pet->name }}" class="w-5 h-5 rounded-full object-cover bg-paw-light">
                                    <span>{{ $pet->name }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-7">
                        <label for="title" class="block text-xs font-bold text-paw-darkest mb-2">
                            Nama kegiatan / jadwal <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               placeholder="Contoh: Mandi & potong kuku"
                               required
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest placeholder:text-paw-medium/60 focus:outline-none focus:border-paw-dark">
                    </div>

                    <div class="sm:col-span-5">
                        <label for="category" class="block text-xs font-bold text-paw-darkest mb-2">
                            Kategori perawatan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="category"
                                    name="category"
                                    required
                                    class="w-full appearance-none pl-4 pr-9 py-2.5 rounded-xl bg-white border border-paw-medium/30 text-xs font-medium text-paw-darkest focus:outline-none focus:border-paw-dark cursor-pointer">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-paw-darkest absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-6">
                        <label for="scheduled_date" class="block text-xs font-bold text-paw-darkest mb-2">
                            Tanggal jadwal <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="scheduled_date"
                               name="scheduled_date"
                               value="{{ old('scheduled_date', date('Y-m-d')) }}"
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest focus:outline-none focus:border-paw-dark">
                    </div>

                    <div class="sm:col-span-6">
                        <label for="scheduled_time" class="block text-xs font-bold text-paw-darkest mb-2">
                            Jam (opsional)
                        </label>
                        <input type="time"
                               id="scheduled_time"
                               name="scheduled_time"
                               lang="id-ID"
                               value="{{ old('scheduled_time') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest focus:outline-none focus:border-paw-dark">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold text-paw-darkest mb-2">
                        Catatan tambahan
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="4"
                              placeholder="Contoh: Gunakan sampo khusus kulit sensitif, keringkan sampai benar-benar kering"
                              class="w-full px-4 py-3 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest placeholder:text-paw-medium/60 focus:outline-none focus:border-paw-dark">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('care-schedules.index') }}"
                       class="px-6 py-2.5 rounded-full bg-white border border-paw-medium/30 hover:border-paw-medium text-paw-darkest text-xs font-semibold transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-full bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold transition">
                        Simpan jadwal
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.querySelectorAll('.pet-radio').forEach((radio) => {
            radio.addEventListener('change', function () {
                document.getElementById('preview-pet-photo').src = this.dataset.photo;
                document.getElementById('preview-pet-name').textContent = this.dataset.name;
                document.getElementById('preview-pet-meta').textContent = this.dataset.meta;
            });
        });
    </script>
@endsection