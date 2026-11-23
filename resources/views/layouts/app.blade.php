<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - PawCare</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-terang.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        paw: {
                            darkest: '#051F20',
                            darker: '#0B2B26',
                            dark: '#163832',
                            medium: '#235347',
                            sage: '#8EB69B',
                            light: '#DAF1DE',
                            cream: '#F4FBF6',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-paw-light text-paw-darkest font-sans antialiased">

    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside id="sidebar"
               style="background: linear-gradient(180deg, #8EB69B 0%, #235347 38%, #0B2B26 75%, #051F20 100%);"
               class="fixed inset-y-0 left-0 z-30 w-64 text-white transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out flex flex-col justify-between shrink-0">

            <div>
                <div class="flex items-center gap-3 px-6 pt-7 pb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="PawCare Logo" class="w-9 h-9 object-contain">
                    <span class="font-heading text-2xl font-extrabold tracking-tight text-paw-darkest">PawCare</span>
                </div>

                <!-- Navigasi Menu -->
                <nav class="px-4 space-y-1.5">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-paw-light text-paw-darkest font-semibold shadow-sm' : 'text-white/90 hover:bg-white/10' }}">
                        <i data-lucide="layout-grid" class="w-4 h-4 shrink-0"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('pets.index') }}"
                        class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pets.*') ? 'bg-paw-light text-paw-darkest font-semibold shadow-sm' : 'text-white/90 hover:bg-white/10' }}">
                        <i data-lucide="paw-print" class="w-4 h-4 shrink-0"></i>
                        <span>My Pets</span>
                    </a>

                    <a href="{{ route('health-records.index') }}"
                       class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('health-records.*') ? 'bg-paw-light text-paw-darkest font-semibold shadow-sm' : 'text-white/90 hover:bg-white/10' }}">
                        <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                        <span>Health Records</span>
                    </a>

                    <a href="{{ route('care-schedules.index') }}"
                        class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('care-schedules.*') ? 'bg-paw-light text-paw-darkest font-semibold shadow-sm' : 'text-white/90 hover:bg-white/10' }}">
                        <i data-lucide="calendar" class="w-4 h-4 shrink-0"></i>
                        <span>Care Schedule</span>
                    </a>

                    <a href="#"
                       class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 transition">
                        <i data-lucide="trending-up" class="w-4 h-4 shrink-0"></i>
                        <span>Growth</span>
                    </a>

                    <a href="#"
                       class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 transition">
                        <i data-lucide="wallet" class="w-4 h-4 shrink-0"></i>
                        <span>Expense</span>
                    </a>

                    <a href="#"
                       class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl text-sm font-medium text-white/90 hover:bg-white/10 transition">
                        <i data-lucide="settings" class="w-4 h-4 shrink-0"></i>
                        <span>Settings</span>
                    </a>
                </nav>
            </div>

            <!-- Profil User & Logout -->
            <div class="p-4 space-y-2.5">
                <div class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl bg-paw-light text-paw-darkest shadow-sm">
                    <div class="w-9 h-9 rounded-full bg-paw-sage/60 text-paw-darkest font-heading font-bold flex items-center justify-center text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-paw-darkest truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-[11px] text-paw-medium truncate">{{ Auth::user()->email ?? 'user@example.com' }}</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-1.5 text-sm font-medium text-red-400 hover:text-red-300 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden"></div>

        <!-- Konten Utama -->
        <div class="flex-1 flex flex-col min-w-0 md:pl-64">

            <!-- Topbar Mobile -->
            <header class="md:hidden flex items-center justify-between bg-paw-darkest text-white px-4 py-3.5">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.png') }}" alt="PawCare" class="w-7 h-7 object-contain">
                    <span class="font-heading font-bold text-lg text-paw-light">PawCare</span>
                </div>
                <button onclick="toggleSidebar()" class="p-2 rounded-lg text-paw-light hover:bg-paw-dark">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </header>

            <main class="flex-1 p-6 md:p-8 max-w-[1280px] w-full mx-auto">
                @yield('content')
            </main>

        </div>
    </div>

    <script>
        lucide.createIcons();

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>