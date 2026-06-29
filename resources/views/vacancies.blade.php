@php
    $locale = app()->getLocale();
    $isAr = $locale == 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isAr ? 'الوظائف الشاغرة - ERP Lite' : 'Vacancies - ERP Lite' }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        dark: '#0f172a'
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .dark .glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: none;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-dark text-slate-800 dark:text-slate-300 font-sans antialiased overflow-x-hidden selection:bg-primary-500 selection:text-white relative min-h-screen flex flex-col transition-colors duration-300">
    
    <!-- Background Orbs -->
    <div class="fixed top-[-10%] right-[-5%] w-[600px] h-[600px] bg-primary-600/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="fixed bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

    <!-- Navigation -->
    <nav class="bg-white/90 dark:bg-slate-900/90 shadow-lg backdrop-blur-md border-b border-slate-200 dark:border-white/5 w-full z-50 transition-all duration-300 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-400 dark:to-primary-600 bg-clip-text text-transparent flex items-center gap-3 hover:opacity-80 transition">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-500 {{ $isAr ? 'rtl:rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        {{ $isAr ? 'العودة للرئيسية' : 'Back to Home' }}
                    </a>
                </div>
                <!-- Language Switcher in Vacancies -->
                <div class="flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition flex items-center justify-center p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    
                    @if($isAr)
                        <a href="{{ route('switch-language', 'en') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition uppercase">EN</a>
                    @else
                        <a href="{{ route('switch-language', 'ar') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition">عربي</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <header class="py-16 text-center border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-slate-900/30">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 tracking-tight">{{ $isAr ? 'الوظائف المتاحة حالياً' : 'Available Vacancies' }}</h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 leading-relaxed">{{ $isAr ? 'اكتشف الفرص التي نقدمها وانضم إلى فريق عمل يتميز بالاحترافية والإبداع.' : 'Discover the opportunities we offer and join a team characterized by professionalism and creativity.' }}</p>
        </div>
    </header>

    <!-- Vacancies List -->
    <main class="flex-grow py-16 relative z-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if(isset($vacancies) && $vacancies->count() > 0)
                @foreach($vacancies as $vacancy)
                <div class="glass p-8 rounded-3xl border border-transparent dark:border-white/10 hover:border-primary-500/50 dark:hover:border-primary-500/50 transition duration-300">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-6">
                        <div>
                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                <span class="inline-block px-3 py-1 bg-primary-100 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400 text-xs font-bold rounded-full">{{ $vacancy->department?->name ?? ($isAr ? 'الشركة' : 'Company') }}</span>
                                <span class="inline-block px-3 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-full">{{ __("filament.employment_type.{$vacancy->employment_type}") ?? ($isAr ? 'دوام كامل' : 'Full Time') }}</span>
                                <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400 bg-slate-200 dark:bg-slate-800 px-3 py-1 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ $isAr ? 'تاريخ النشر:' : 'Posted:' }} {{ $vacancy->created_at->format('Y-m-d') }}
                                </span>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">{{ $vacancy->title }}</h2>
                            <div class="flex flex-wrap items-center gap-6 text-sm text-slate-600 dark:text-slate-400">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $vacancy->location ?? ($isAr ? 'المقر الرئيسي' : 'HQ') }}
                                </span>
                                @if($vacancy->salary_range)
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $vacancy->salary_range }}
                                </span>
                                @endif
                                <span class="flex items-center gap-1.5 text-primary-600 dark:text-primary-400 font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    {{ $isAr ? 'عدد الشواغر:' : 'Positions:' }} {{ $vacancy->positions_count }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-shrink-0 mt-4 md:mt-0 w-full md:w-auto">
                            <a href="/register" class="inline-flex items-center justify-center bg-primary-600 hover:bg-primary-500 text-white px-8 py-3.5 rounded-xl font-bold transition shadow-lg shadow-primary-600/20 whitespace-nowrap w-full md:w-auto">
                                {{ $isAr ? 'التقدم الآن' : 'Apply Now' }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-200 dark:border-white/5 pt-6 mt-6 text-slate-700 dark:text-slate-300 leading-relaxed space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $isAr ? 'وصف الوظيفة' : 'Job Description' }}
                            </h3>
                            <div class="text-slate-600 dark:text-slate-400 text-sm whitespace-pre-line">{{ $vacancy->description }}</div>
                        </div>
                        
                        @if($vacancy->requirements)
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $isAr ? 'المتطلبات' : 'Requirements' }}
                            </h3>
                            <div class="text-slate-600 dark:text-slate-400 text-sm whitespace-pre-line">{{ $vacancy->requirements }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
                
                @if($vacancies->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="glass px-4 py-2 rounded-xl">
                        {{ $vacancies->links() }}
                    </div>
                </div>
                @endif
            @else
                <div class="glass p-16 rounded-3xl text-center border border-transparent dark:border-white/5">
                    <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">{{ $isAr ? 'لا توجد وظائف شاغرة حالياً' : 'No Vacancies Available' }}</h3>
                    <p class="text-lg text-slate-600 dark:text-slate-400">{{ $isAr ? 'يرجى العودة والتحقق في وقت لاحق للفرص الجديدة.' : 'Please check back later for new opportunities.' }}</p>
                </div>
            @endif
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-white/5 py-8 bg-slate-50 dark:bg-dark relative z-10 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-500 text-sm">
            {{ $isAr ? 'جميع الحقوق محفوظة' : 'All Rights Reserved' }} &copy; {{ date('Y') }} ERP Lite
        </div>
    </footer>

    <script>
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</body>
</html>
