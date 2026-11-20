@extends('layouts.app')

@section('title', 'Edit ' . $pet->name)

@section('content')
    <!-- Link Kembali -->
    <a href="{{ route('pets.show', $pet->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-paw-medium hover:text-paw-darkest mb-3">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        <span>Kembali ke detail {{ $pet->name }}</span>
    </a>

    <!-- Header Halaman -->
    <div class="mb-7">
        <h1 class="font-heading text-2xl md:text-3xl font-extrabold text-paw-darkest">Edit profil hewan</h1>
        <p class="text-xs md:text-sm text-paw-medium mt-1">Perbarui informasi dasar dan catatan kesehatan {{ $pet->name }}.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl text-xs">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pets.update', $pet->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="flex flex-col lg:flex-row items-start gap-8">
            <!-- Sisi Kiri: Kotak Preview Foto & Ringkasan Real-Time -->
            <div class="w-full sm:w-[270px] shrink-0 flex flex-col items-start">
                <div class="w-full aspect-square rounded-3xl bg-paw-sage/85 border border-paw-medium/30 overflow-hidden flex items-center justify-center">
                    <img id="photoPreviewImg"
                         src="{{ $pet->photo ? asset('storage/' . $pet->photo) : asset('images/default.png') }}"
                         alt="{{ $pet->name }}"
                         class="w-full h-full object-cover">
                </div>

                <!-- Teks Preview Nama, Jenis & Kelamin -->
                <div class="mt-4">
                    <h2 id="previewName" class="font-heading text-xl font-extrabold text-paw-darkest leading-tight">
                        {{ old('name', $pet->name) }}
                    </h2>
                    <p id="previewMeta" class="text-xs text-paw-medium mt-0.5">
                        {{ old('species', $pet->species) }} • {{ old('gender', $pet->gender) }}
                    </p>
                </div>

                <!-- Tombol Ganti Foto -->
                <label class="mt-3.5 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold cursor-pointer transition">
                    <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                    <span>Ganti Foto</span>
                    <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg" class="hidden">
                </label>

                <p class="text-[11px] text-paw-medium mt-2">
                    Opsional. Format JPG atau PNG, maksimal 2 MB.
                </p>
            </div>

            <!-- Sisi Kanan: Form Input Terbuka -->
            <div class="flex-1 w-full space-y-5">
                <!-- Nama Hewan -->
                <div>
                    <label for="nameInput" class="block text-xs font-bold text-paw-darkest mb-1.5">
                        Nama hewan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nameInput" name="name" value="{{ old('name', $pet->name) }}" required
                           placeholder="Contoh: Mochi"
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest placeholder-paw-medium/50 focus:outline-none focus:border-paw-medium">
                </div>

                <!-- Jenis Hewan (Pill Radio) -->
                <div>
                    <label class="block text-xs font-bold text-paw-darkest mb-2">
                        Jenis hewan <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach(['Kucing', 'Anjing', 'Kelinci', 'Burung', 'Reptil'] as $sp)
                            <label class="cursor-pointer">
                                <input type="radio" name="species" value="{{ $sp }}" class="peer sr-only species-radio"
                                       {{ old('species', $pet->species) === $sp ? 'checked' : '' }}>
                                <span class="inline-block px-4 py-2 rounded-full text-xs font-semibold bg-white text-paw-darkest border border-paw-medium/25 peer-checked:bg-paw-dark peer-checked:text-white peer-checked:border-paw-dark transition">
                                    {{ $sp }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Ras / Breed & Jenis Kelamin -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    <div class="sm:col-span-7">
                        <label class="block text-xs font-bold text-paw-darkest mb-1.5">Ras / breed</label>
                        <input type="text" name="breed" value="{{ old('breed', $pet->breed) }}"
                               placeholder="Contoh: Persia, Domestik"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest placeholder-paw-medium/50 focus:outline-none focus:border-paw-medium">
                    </div>

                    <div class="sm:col-span-5">
                        <label class="block text-xs font-bold text-paw-darkest mb-1.5">
                            Jenis kelamin <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2.5">
                            @foreach(['Jantan', 'Betina'] as $gd)
                                <label class="cursor-pointer">
                                    <input type="radio" name="gender" value="{{ $gd }}" class="peer sr-only gender-radio"
                                           {{ old('gender', $pet->gender) === $gd ? 'checked' : '' }}>
                                    <span class="inline-block px-4 py-2 rounded-full text-xs font-semibold bg-white text-paw-darkest border border-paw-medium/25 peer-checked:bg-paw-dark peer-checked:text-white peer-checked:border-paw-dark transition">
                                        {{ $gd }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tanggal Lahir & Berat Badan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-paw-darkest mb-1.5">Tanggal lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $pet->birth_date) }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest focus:outline-none focus:border-paw-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-paw-darkest mb-1.5">Berat badan</label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" name="weight" value="{{ old('weight', $pet->weight) }}"
                                   placeholder="Contoh: 3,5"
                                   class="w-full pl-4 pr-10 py-2.5 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest placeholder-paw-medium/50 focus:outline-none focus:border-paw-medium">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-medium text-paw-medium">kg</span>
                        </div>
                    </div>
                </div>

                <!-- Catatan Khusus / Alergi -->
                <div>
                    <label class="block text-xs font-bold text-paw-darkest mb-1.5">Catatan khusus / alergi</label>
                    <textarea name="notes" rows="4"
                              placeholder="Contoh: Alergi makanan laut, butuh vitamin bulu rutin"
                              class="w-full px-4 py-3 rounded-xl bg-white border border-paw-medium/30 text-xs text-paw-darkest placeholder-paw-medium/50 focus:outline-none focus:border-paw-medium">{{ old('notes', $pet->notes) }}</textarea>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('pets.show', $pet->id) }}"
                       class="px-5 py-2.5 rounded-full bg-white border border-paw-medium/30 hover:border-paw-medium text-paw-darkest text-xs font-semibold transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-full bg-paw-dark hover:bg-paw-darkest text-white text-xs font-semibold transition">
                        Simpan perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('nameInput');
            const previewName = document.getElementById('previewName');
            const previewMeta = document.getElementById('previewMeta');
            const speciesRadios = document.querySelectorAll('.species-radio');
            const genderRadios = document.querySelectorAll('.gender-radio');
            const photoInput = document.getElementById('photoInput');
            const photoPreviewImg = document.getElementById('photoPreviewImg');

            function updateMeta() {
                const selectedSpecies = document.querySelector('.species-radio:checked')?.value || 'Kucing';
                const selectedGender = document.querySelector('.gender-radio:checked')?.value || 'Jantan';
                previewMeta.textContent = `${selectedSpecies} • ${selectedGender}`;
            }

            nameInput.addEventListener('input', function () {
                previewName.textContent = this.value.trim() !== '' ? this.value : 'Nama hewan';
            });

            speciesRadios.forEach(radio => radio.addEventListener('change', updateMeta));
            genderRadios.forEach(radio => radio.addEventListener('change', updateMeta));

            photoInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        photoPreviewImg.src = ev.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection