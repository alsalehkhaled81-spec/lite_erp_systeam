@php
    $locale = app()->getLocale();
    $isAr = $locale == 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isAr ? 'ERP Lite - نظام الإدارة المركزي' : 'ERP Lite - Central Management System' }}</title>
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
                    },
                    animation: {
                        'float': 'float 5s ease-in-out infinite',
                        'float-delayed': 'float 5s ease-in-out infinite 2.5s',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' },
                        }
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
<body class="bg-slate-50 dark:bg-dark text-slate-800 dark:text-slate-300 font-sans antialiased overflow-x-hidden selection:bg-primary-500 selection:text-white relative transition-colors duration-300">
    
    <!-- Background Orbs -->
    <div class="fixed top-[-10%] right-[-5%] w-[600px] h-[600px] bg-primary-600/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="fixed bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[120px] -z-10 pointer-events-none"></div>

    <!-- Navigation -->
    <nav x-data="{ scrolled: false, mobileMenuOpen: false }" 
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="{ 'bg-white/90 dark:bg-slate-900/90 shadow-lg backdrop-blur-md border-b border-slate-200 dark:border-white/5': scrolled, 'bg-transparent border-transparent': !scrolled }"
         class="fixed w-full z-50 transition-all duration-300 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-400 dark:to-primary-600 bg-clip-text text-transparent">
                        ERP Lite
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8 space-x-reverse">
                        <a href="#home" class="text-slate-800 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-semibold transition">{{ $isAr ? 'الرئيسية' : 'Home' }}</a>
                        <a href="#about" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-semibold transition">{{ $isAr ? 'من نحن' : 'About Us' }}</a>
                        <a href="#services" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-semibold transition">{{ $isAr ? 'خدماتنا' : 'Services' }}</a>
                        <a href="#vacancies" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-semibold transition">{{ $isAr ? 'الوظائف الشاغرة' : 'Vacancies' }}</a>
                        <a href="#contact" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-semibold transition">{{ $isAr ? 'تواصل معنا' : 'Contact Us' }}</a>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <!-- Language Switcher -->
                    @if($isAr)
                        <a href="{{ route('switch-language', 'en') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition uppercase">EN</a>
                    @else
                        <a href="{{ route('switch-language', 'ar') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition">عربي</a>
                    @endif
                    
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition flex items-center justify-center p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                    
                    <div class="w-px h-5 bg-slate-300 dark:bg-slate-700"></div>
                    
                    <a href="/login" class="text-sm font-semibold text-slate-800 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition">{{ $isAr ? 'تسجيل الدخول' : 'Login' }}</a>
                    <a href="/register" class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg shadow-primary-600/30">{{ $isAr ? 'إنشاء حساب' : 'Register' }}</a>
                </div>

                <!-- Mobile menu button -->
                <div class="-mr-2 flex md:hidden items-center gap-4">
                    <!-- Mobile Language Switcher -->
                    @if($isAr)
                        <a href="{{ route('switch-language', 'en') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase">EN</a>
                    @else
                        <a href="{{ route('switch-language', 'ar') }}" class="text-sm font-bold text-slate-600 dark:text-slate-300">عربي</a>
                    @endif
                    
                    <!-- Mobile Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition p-2">
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>

                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             class="md:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-white/5 absolute w-full shadow-lg"
             @click.away="mobileMenuOpen = false">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#home" @click="mobileMenuOpen = false" class="text-slate-800 dark:text-white block px-3 py-2 rounded-md text-base font-medium">{{ $isAr ? 'الرئيسية' : 'Home' }}</a>
                <a href="#about" @click="mobileMenuOpen = false" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white block px-3 py-2 rounded-md text-base font-medium">{{ $isAr ? 'من نحن' : 'About Us' }}</a>
                <a href="#services" @click="mobileMenuOpen = false" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white block px-3 py-2 rounded-md text-base font-medium">{{ $isAr ? 'خدماتنا' : 'Services' }}</a>
                <a href="#vacancies" @click="mobileMenuOpen = false" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white block px-3 py-2 rounded-md text-base font-medium">{{ $isAr ? 'الوظائف الشاغرة' : 'Vacancies' }}</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white block px-3 py-2 rounded-md text-base font-medium">{{ $isAr ? 'تواصل معنا' : 'Contact Us' }}</a>
            </div>
            <div class="pt-4 pb-4 border-t border-slate-200 dark:border-white/10 px-5 flex flex-col gap-3">
                <a href="/login" class="text-center w-full block text-slate-800 dark:text-slate-300 font-semibold py-2">{{ $isAr ? 'تسجيل الدخول' : 'Login' }}</a>
                <a href="/register" class="text-center w-full block bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-semibold transition">{{ $isAr ? 'إنشاء حساب' : 'Register' }}</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 flex items-center justify-center min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass border border-primary-500/30 text-primary-600 dark:text-primary-300 text-sm font-semibold mb-8">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-primary-500"></span>
                </span>
                {{ $isAr ? 'نظام إدارة مركزي متكامل ذكي' : 'Smart Integrated Central Management System' }}
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-8 leading-tight">
                {{ $isAr ? 'أدر أعمالك بذكاء مع' : 'Manage your business smartly with' }} <br/>
                <span class="bg-gradient-to-r from-primary-600 to-purple-600 dark:from-primary-400 dark:to-purple-500 bg-clip-text text-transparent">ERP Lite</span>
            </h1>
            
            <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto mb-10 leading-relaxed">
                {{ $isAr ? 'منصة سحابية متكاملة تجمع بين إدارة الموارد البشرية، المشاريع، المالية، والمهام. صممت لرفع كفاءة فريقك وتسريع وتيرة نمو شركتك.' : 'An integrated cloud platform combining HR, projects, finance, and tasks management. Designed to boost your team efficiency and accelerate your company growth.' }}
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/register" class="bg-primary-600 hover:bg-primary-500 text-white px-8 py-4 rounded-2xl text-lg font-bold transition shadow-lg shadow-primary-600/30 flex items-center justify-center gap-2">
                    {{ $isAr ? 'ابدأ تجربتك الآن' : 'Start your experience now' }}
                    <svg class="w-5 h-5 {{ $isAr ? 'rtl:rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="#services" class="glass hover:bg-slate-100 dark:hover:bg-white/10 text-slate-800 dark:text-white px-8 py-4 rounded-2xl text-lg font-bold transition flex items-center justify-center">
                    {{ $isAr ? 'اكتشف الميزات' : 'Discover Features' }}
                </a>
            </div>
            
            <!-- Quick Stats -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 border-t border-slate-200 dark:border-white/10 pt-10">
                <div class="text-center">
                    <p class="text-4xl font-black text-primary-600 dark:text-white mb-2">{{ $stats['employees'] ?? 0 }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">{{ $isAr ? 'موظف نشط' : 'Active Employee' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary-600 dark:text-white mb-2">{{ $stats['projects'] ?? 0 }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">{{ $isAr ? 'مشروع' : 'Project' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary-600 dark:text-white mb-2">{{ $stats['tasks_completed'] ?? 0 }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">{{ $isAr ? 'مهمة منجزة' : 'Completed Task' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-black text-primary-600 dark:text-white mb-2">{{ $stats['clients'] ?? 0 }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">{{ $isAr ? 'عميل' : 'Client' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                <div class="mb-12 lg:mb-0">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-6">{{ $isAr ? 'من نحن؟ رؤية تقنية لمستقبل الأعمال' : 'Who we are? Technical vision for future business' }}</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                        {{ $isAr ? 'نحن في ERP Lite نؤمن بأن الإدارة يجب أن تكون سلسة وذكية ومترابطة. قمنا بتطوير هذا النظام ليكون الحل الأمثل للشركات التي تبحث عن الكفاءة والتميز دون التعقيدات المعتادة للأنظمة الكبيرة.' : 'At ERP Lite, we believe management should be seamless, smart, and interconnected. We developed this system to be the optimal solution for companies looking for efficiency and excellence without the usual complexities of large systems.' }}
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-slate-700 dark:text-slate-300">{{ $isAr ? 'أدوات متطورة تعتمد على الذكاء الاصطناعي لتحليل البيانات.' : 'Advanced AI-based tools for data analysis.' }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-slate-700 dark:text-slate-300">{{ $isAr ? 'واجهات استخدام عصرية مصممة لراحة العين وسرعة الإنجاز.' : 'Modern user interfaces designed for eye comfort and speed of achievement.' }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-500/20 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-slate-700 dark:text-slate-300">{{ $isAr ? 'أمان عالي وموثوقية في حفظ وإدارة بيانات شركتك.' : 'High security and reliability in saving and managing your company data.' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-primary-400 to-purple-400 dark:from-primary-600 dark:to-purple-600 rounded-3xl transform rotate-3 opacity-30 dark:opacity-50 blur-lg"></div>
                    <div class="glass p-8 rounded-3xl relative">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/80 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm animate-float">
                                <svg class="w-10 h-10 text-primary-500 dark:text-primary-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $isAr ? 'سرعة الأداء' : 'Fast Performance' }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $isAr ? 'تقنيات حديثة تضمن استجابة فورية.' : 'Modern technologies ensure instant response.' }}</p>
                            </div>
                            <!-- Added parent wrapper for translate-y-6 so animation works properly inside -->
                            <div class="transform translate-y-6">
                                <div class="bg-white/80 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm animate-float-delayed">
                                    <svg class="w-10 h-10 text-purple-500 dark:text-purple-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $isAr ? 'حماية متقدمة' : 'Advanced Security' }}</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $isAr ? 'تشفير كامل وصلاحيات دقيقة.' : 'Full encryption and precise permissions.' }}</p>
                                </div>
                            </div>
                            <div class="bg-white/80 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm animate-float-delayed">
                                <svg class="w-10 h-10 text-emerald-500 dark:text-emerald-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $isAr ? 'تقارير دقيقة' : 'Accurate Reports' }}</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $isAr ? 'إحصائيات ورسوم بيانية محدثة.' : 'Updated stats and charts.' }}</p>
                            </div>
                            <div class="transform translate-y-6">
                                <div class="bg-white/80 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-200 dark:border-white/5 shadow-sm animate-float">
                                    <svg class="w-10 h-10 text-rose-500 dark:text-rose-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $isAr ? 'فريق مترابط' : 'Connected Team' }}</h3>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $isAr ? 'تواصل ومشاركة للملفات بسهولة.' : 'Easy communication and file sharing.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-24 bg-slate-100 dark:bg-slate-900/50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">{{ $isAr ? 'خدماتنا ونظامنا المتكامل' : 'Our Services and Integrated System' }}</h2>
                <p class="text-lg text-slate-600 dark:text-slate-400">{{ $isAr ? 'يغطي ERP Lite كافة الأقسام الرئيسية في شركتك لضمان سير العمل بمرونة وسهولة.' : 'ERP Lite covers all main departments in your company to ensure a flexible workflow.' }}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition duration-300 group border border-transparent dark:border-white/5 hover:border-blue-500/30 dark:hover:border-blue-500/50">
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-200 dark:group-hover:bg-blue-500/20 transition">
                        <svg class="w-7 h-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ $isAr ? 'إدارة الموارد البشرية (HR)' : 'Human Resources (HR)' }}</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $isAr ? 'نظام كامل لإدارة الموظفين، الحضور والانصراف، الإجازات، والرواتب مع تقييم أداء شامل واستقطاب المواهب.' : 'Full system for managing employees, attendance, leaves, and payroll with performance evaluation.' }}</p>
                </div>
                
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition duration-300 group border border-transparent dark:border-white/5 hover:border-purple-500/30 dark:hover:border-purple-500/50">
                    <div class="w-14 h-14 bg-purple-100 dark:bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/20 transition">
                        <svg class="w-7 h-7 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ $isAr ? 'إدارة المشاريع والمهام (PM)' : 'Project Management (PM)' }}</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $isAr ? 'تخطيط وتتبع المشاريع، توزيع المهام، لوحات كانبان التفاعلية، ومراقبة الجداول الزمنية بكل دقة.' : 'Planning and tracking projects, task distribution, interactive Kanban boards, and timeline monitoring.' }}</p>
                </div>
                
                <div class="glass p-8 rounded-3xl hover:-translate-y-2 transition duration-300 group border border-transparent dark:border-white/5 hover:border-emerald-500/30 dark:hover:border-emerald-500/50">
                    <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-200 dark:group-hover:bg-emerald-500/20 transition">
                        <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ $isAr ? 'الإدارة المالية (Finance)' : 'Financial Management' }}</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $isAr ? 'تسجيل المصروفات، إصدار الفواتير للعملاء، ومتابعة الميزانيات بدقة متناهية مع تقارير مالية مفصلة.' : 'Recording expenses, issuing client invoices, and tracking budgets accurately with detailed reports.' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Vacancies Section -->
    <section id="vacancies" class="py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">{{ $isAr ? 'أحدث الوظائف المتاحة' : 'Latest Available Vacancies' }}</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400">{{ $isAr ? 'انضم إلى فريقنا واصنع الفارق. تصفح أحدث الفرص الوظيفية المتاحة لدينا.' : 'Join our team and make a difference. Browse our latest job opportunities.' }}</p>
                </div>
                <a href="{{ route('vacancies.index') }}" class="inline-flex items-center gap-2 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-900 dark:text-white px-6 py-3 rounded-xl font-semibold transition border border-slate-300 dark:border-slate-700 whitespace-nowrap">
                    {{ $isAr ? 'تصفح جميع الوظائف' : 'Browse All Jobs' }}
                    <svg class="w-5 h-5 {{ $isAr ? 'rtl:rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            @if(isset($vacancies) && count($vacancies) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($vacancies as $vacancy)
                    <div class="glass p-6 rounded-2xl flex flex-col h-full border border-slate-200 dark:border-white/10 hover:border-primary-500/50 transition duration-300 bg-white dark:bg-slate-800/30 text-start">
                        <div class="mb-4">
                            <span class="inline-block px-3 py-1 bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400 text-xs font-bold rounded-full mb-3">{{ $vacancy->department?->name ?? ($isAr ? 'عام' : 'General') }}</span>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $vacancy->title }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3">{{ $vacancy->description }}</p>
                        </div>
                        
                        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-white/5">
                            <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-300 mb-6">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $vacancy->location ?? ($isAr ? 'الشركة' : 'Company') }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $isAr ? (['full_time' => 'دوام كامل', 'part_time' => 'دوام جزئي', 'contract' => 'عقد', 'freelance' => 'عمل حر', 'internship' => 'تدريب'][$vacancy->employment_type] ?? 'دوام كامل') : (['full_time' => 'Full Time', 'part_time' => 'Part Time', 'contract' => 'Contract', 'freelance' => 'Freelance', 'internship' => 'Internship'][$vacancy->employment_type] ?? 'Full Time') }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    {{ $vacancy->applicants_count }} {{ $isAr ? 'متقدم' : 'Applicants' }}
                                </span>
                            </div>
                            <a href="/register" class="w-full block text-center bg-primary-600 hover:bg-primary-500 text-white px-4 py-2.5 rounded-xl font-bold transition">
                                {{ $isAr ? 'التقدم للوظيفة' : 'Apply Now' }}
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="glass p-12 rounded-3xl text-center border border-slate-200 dark:border-white/5">
                    <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $isAr ? 'لا توجد وظائف شاغرة حالياً' : 'No Vacancies Available' }}</h3>
                    <p class="text-slate-600 dark:text-slate-400">{{ $isAr ? 'يرجى العودة والتحقق في وقت لاحق للفرص الجديدة.' : 'Please check back later for new opportunities.' }}</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-24 bg-slate-100 dark:bg-slate-900/50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16">
                <div class="mb-12 lg:mb-0 text-start">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-6">{{ $isAr ? 'تواصل معنا' : 'Contact Us' }}</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-10 leading-relaxed">
                        {{ $isAr ? 'نحن هنا لمساعدتك والإجابة على كافة استفساراتك حول النظام وكيفية الاستفادة منه في أعمالك.' : 'We are here to help and answer all your inquiries about the system.' }}
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center flex-shrink-0 border border-slate-200 dark:border-white/5 shadow-sm">
                                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="text-start">
                                <h4 class="text-slate-900 dark:text-white font-bold mb-1">{{ $isAr ? 'العنوان' : 'Address' }}</h4>
                                <p class="text-slate-600 dark:text-slate-400">{{ $isAr ? 'دمشق، الجمهورية العربية السورية' : 'Damascus, Syrian Arab Republic' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center flex-shrink-0 border border-slate-200 dark:border-white/5 shadow-sm">
                                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="text-start">
                                <h4 class="text-slate-900 dark:text-white font-bold mb-1">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</h4>
                                <p class="text-slate-600 dark:text-slate-400">info@erplite.com</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <form class="glass p-8 rounded-3xl space-y-5 border border-slate-200 dark:border-white/10 text-start">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ $isAr ? 'الاسم الكامل' : 'Full Name' }}</label>
                            <input type="text" class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" placeholder="{{ $isAr ? 'أدخل اسمك' : 'Enter your name' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ $isAr ? 'البريد الإلكتروني' : 'Email Address' }}</label>
                            <input type="email" class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" placeholder="your@email.com">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ $isAr ? 'الرسالة' : 'Message' }}</label>
                            <textarea rows="4" class="w-full bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" placeholder="{{ $isAr ? 'كيف يمكننا مساعدتك؟' : 'How can we help you?' }}"></textarea>
                        </div>
                        <button type="button" class="w-full bg-primary-600 hover:bg-primary-500 text-white px-6 py-3.5 rounded-xl font-bold transition shadow-lg shadow-primary-600/20">
                            {{ $isAr ? 'إرسال الرسالة' : 'Send Message' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-white/5 py-10 bg-slate-50 dark:bg-dark relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-400 dark:to-primary-600 bg-clip-text text-transparent">
                ERP Lite
            </div>
            <p class="text-slate-500 text-sm">
                {{ $isAr ? 'جميع الحقوق محفوظة' : 'All Rights Reserved' }} &copy; {{ date('Y') }} ERP Lite
            </p>
            <div class="flex gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-white dark:hover:bg-primary-600 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-primary-600 dark:hover:text-white dark:hover:bg-primary-600 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
            </div>
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
