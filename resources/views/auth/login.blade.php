<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - PawCare</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-terang.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { poppins: ['Poppins', 'sans-serif'], inter: ['Inter', 'sans-serif'] },
                    colors: { pawDeepest: '#051F20', pawDark: '#0B2B26', pawForest: '#163832', pawGreen: '#235347', pawSage: '#8EB69B', pawMint: '#DAF1DE' }
                }
            }
        }
    </script>
</head>
<body class="font-inter min-h-screen flex flex-col md:flex-row">

    <!-- Panel kiri -->
    <div class="w-full md:w-1/2 bg-gradient-to-b from-pawSage via-pawGreen to-pawDark text-white p-8 md:p-12 flex flex-col justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo PawCare" class="w-10 h-10 object-contain">
            <span class="font-poppins font-bold text-2xl text-pawDeepest">PawCare</span>
        </div>

        <img src="{{ asset('images/catanddog.png') }}" alt="Ilustrasi" class="w-64 md:w-96 mx-auto my-6">

        <div class="max-w-md">
            <h2 class="font-poppins font-bold text-2xl md:text-3xl leading-snug">Sahabat berbulu anda, terawat dengan baik</h2>
            <p class="text-xs md:text-sm text-pawMint/80 mt-2">Pantau semua kesehatan dan perawatan hewan peliharaan anda dalam satu aplikasi.</p>
        </div>
    </div>

    <!-- Panel kanan login -->
    <div class="w-full md:w-1/2 bg-pawMint flex items-center justify-center p-8 md:p-16">
        <div class="w-full max-w-md">
            <h1 class="font-poppins font-bold text-3xl text-pawDeepest">Selamat datang kembali</h1>
            <p class="text-sm text-pawForest/80 mt-1 mb-6">Masuk untuk melihat kondisi hewan peliharaan Anda.</p>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-700 text-xs p-3 rounded-xl mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-pawDeepest mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                        class="w-full px-4 py-3 rounded-xl bg-white text-sm text-pawDeepest focus:outline-none focus:ring-2 focus:ring-pawGreen">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-pawDeepest mb-1">Kata sandi</label>
                    <input type="password" name="password" placeholder="Masukkan kata sandi" required
                        class="w-full px-4 py-3 rounded-xl bg-white text-sm text-pawDeepest focus:outline-none focus:ring-2 focus:ring-pawGreen">
                </div>

                <button type="submit" class="w-full bg-pawForest hover:bg-pawDark text-white font-poppins font-semibold py-3 rounded-full text-sm transition mt-2">
                    Masuk
                </button>
            </form>

            <p class="text-center text-xs text-pawForest/80 mt-6">
                Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-pawDeepest hover:underline">Daftar sekarang</a>
            </p>
        </div>
    </div>

</body>
</html>