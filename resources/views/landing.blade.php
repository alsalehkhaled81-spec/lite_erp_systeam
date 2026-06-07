<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Lite - نظام الإدارة المركزي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Tajawal', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 30%, #312e81 60%, #1e293b 100%);
            color: #e2e8f0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            direction: rtl;
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.3;
            pointer-events: none;
            z-index: 0;
            animation: floatOrb 14s ease-in-out infinite;
        }
        .orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.3), transparent);
            top: -10%; right: -5%;
        }
        .orb-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(59,130,246,0.25), transparent);
            bottom: 10%; left: -5%;
            animation-delay: -7s;
        }
        .orb-3 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(168,85,247,0.2), transparent);
            top: 40%; left: 30%;
            animation-delay: -3s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(25px, -25px) scale(1.03); }
            66% { transform: translate(-15px, 15px) scale(0.97); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 100;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(15, 23, 42, 0.85);
            box-shadow: 0 4px 30px rgba(0,0,0,0.3);
        }

        .nav-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #818cf8, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover { color: #ffffff; }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-ghost {
            padding: 0.55rem 1.25rem;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            color: #e2e8f0;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-ghost:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.25);
        }

        .btn-primary {
            padding: 0.55rem 1.25rem;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            border-radius: 10px;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px rgba(99,102,241,0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99,102,241,0.35);
        }

        .btn-primary:active { transform: translateY(0); }

        .btn-lg {
            padding: 0.9rem 2rem;
            font-size: 1rem;
            border-radius: 14px;
        }

        .btn-outline-lg {
            padding: 0.9rem 2rem;
            font-size: 1rem;
            border-radius: 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            color: #e2e8f0;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-outline-lg:hover {
            background: rgba(255,255,255,0.12);
            transform: translateY(-2px);
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #e2e8f0;
            cursor: pointer;
            padding: 0.5rem;
        }

        .hero {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
        }

        .hero-content {
            max-width: 800px;
            animation: fadeInUp 0.8s ease;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 1.2rem;
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 50px;
            color: #a5b4fc;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: 3.2rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.3;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .hero p {
            font-size: 1.15rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.8;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-right: auto;
            margin-left: auto;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .section {
            position: relative;
            z-index: 1;
            padding: 5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .section-title h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }

        .section-title p {
            font-size: 1rem;
            color: rgba(255,255,255,0.4);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .feature-card {
            padding: 2rem;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.07);
            border-color: rgba(255,255,255,0.12);
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(99,102,241,0.05));
            border-radius: 14px;
            margin-bottom: 1.25rem;
        }

        .feature-icon svg {
            width: 26px;
            height: 26px;
            stroke: #818cf8;
        }

        .feature-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.6rem;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.45);
            line-height: 1.7;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .stat-card {
            text-align: center;
            padding: 2.5rem 1.5rem;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            background: rgba(255,255,255,0.07);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #818cf8, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-card:nth-child(2) .stat-number {
            background: linear-gradient(135deg, #34d399, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card:nth-child(3) .stat-number {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card:nth-child(4) .stat-number {
            background: linear-gradient(135deg, #f472b6, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
        }

        .panels-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .panel-card {
            padding: 2rem;
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px;
            border-top: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .panel-card:hover {
            transform: translateY(-6px);
            background: rgba(255,255,255,0.07);
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .panel-card.amber { border-top-color: #f59e0b; }
        .panel-card.rose { border-top-color: #f43f5e; }
        .panel-card.blue { border-top-color: #3b82f6; }
        .panel-card.emerald { border-top-color: #10b981; }
        .panel-card.indigo { border-top-color: #6366f1; }

        .panel-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-left: 0.5rem;
        }

        .panel-card.amber .panel-dot { background: #f59e0b; }
        .panel-card.rose .panel-dot { background: #f43f5e; }
        .panel-card.blue .panel-dot { background: #3b82f6; }
        .panel-card.emerald .panel-dot { background: #10b981; }
        .panel-card.indigo .panel-dot { background: #6366f1; }

        .panel-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .panel-card p {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.45);
            line-height: 1.7;
        }

        .cta-section {
            position: relative;
            z-index: 1;
            padding: 5rem 2rem;
            text-align: center;
        }

        .cta-box {
            max-width: 700px;
            margin: 0 auto;
            padding: 4rem 3rem;
            background: rgba(99,102,241,0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(99,102,241,0.2);
            border-radius: 28px;
        }

        .cta-box h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }

        .cta-box p {
            font-size: 1rem;
            color: rgba(255,255,255,0.45);
            margin-bottom: 2rem;
        }

        .footer {
            position: relative;
            z-index: 1;
            padding: 2.5rem 2rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-copy {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.35);
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-links a {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-links a:hover { color: #a5b4fc; }

        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.97);
            backdrop-filter: blur(20px);
            z-index: 200;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2rem;
        }

        .mobile-menu.open { display: flex; }

        .mobile-menu a {
            color: #e2e8f0;
            text-decoration: none;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .mobile-close {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: none;
            border: none;
            color: #e2e8f0;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .nav-links, .nav-actions { display: none; }
            .mobile-toggle { display: block; }

            .hero h1 { font-size: 2rem; }
            .hero p { font-size: 1rem; }
            .hero { padding: 7rem 1.5rem 3rem; }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .stat-number { font-size: 2.2rem; }

            .panels-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .section { padding: 3rem 1.5rem; }

            .section-title h2 { font-size: 1.6rem; }

            .cta-box { padding: 2.5rem 1.5rem; }
            .cta-box h2 { font-size: 1.5rem; }

            .footer-content { flex-direction: column; text-align: center; }

            .hero-actions { flex-direction: column; }
            .hero-actions a, .hero-actions button { width: 100%; text-align: center; }
        }

        @media (max-width: 480px) {
            .features-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <nav class="navbar" id="navbar">
        <a href="/" class="nav-logo">ERP Lite</a>

        <ul class="nav-links">
            <li><a href="#features">الميزات</a></li>
            <li><a href="#stats">الإحصائيات</a></li>
            <li><a href="#contact">تواصل معنا</a></li>
        </ul>

        <div class="nav-actions">
            <a href="/login" class="btn-ghost">تسجيل الدخول</a>
            <a href="/register" class="btn-primary">إنشاء حساب</a>
        </div>

        <button class="mobile-toggle" onclick="document.getElementById('mobileMenu').classList.add('open')" aria-label="القائمة">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </nav>

    <div class="mobile-menu" id="mobileMenu">
        <button class="mobile-close" onclick="document.getElementById('mobileMenu').classList.remove('open')">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <a href="#features" onclick="document.getElementById('mobileMenu').classList.remove('open')">الميزات</a>
        <a href="#stats" onclick="document.getElementById('mobileMenu').classList.remove('open')">الإحصائيات</a>
        <a href="#contact" onclick="document.getElementById('mobileMenu').classList.remove('open')">تواصل معنا</a>
        <a href="/login" class="btn-ghost">تسجيل الدخول</a>
        <a href="/register" class="btn-primary">إنشاء حساب</a>
    </div>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                نظام إدارة مركزي متكامل
            </div>
            <h1>نظام إدارة مركزي متكامل لإدارة أعمالك</h1>
            <p>منصة واحدة تجمع بين إدارة الموظفين، المشاريع، المالية، والمهام بكفاءة عالية</p>
            <div class="hero-actions">
                <a href="/register" class="btn-primary btn-lg">ابدأ الآن</a>
                <a href="#features" class="btn-outline-lg">تعرف على المزيد</a>
            </div>
        </div>
    </section>

    <section class="section" id="features">
        <div class="section-title animate-on-scroll">
            <h2>الميزات الرئيسية</h2>
            <p>كل ما تحتاجه لإدارة أعمالك في مكان واحد</p>
        </div>
        <div class="features-grid">
            <div class="feature-card animate-on-scroll">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <h3>إدارة الموظفين</h3>
                <p>إدارة شاملة للموظفين من التعيين حتى التقاعد</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </div>
                <h3>إدارة المشاريع</h3>
                <p>تخطيط وتتبع المشاريع بأدوات كانبان تفاعلية</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                    </svg>
                </div>
                <h3>النظام المالي</h3>
                <p>فواتير، مصروفات، ورواتب في مكان واحد</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/>
                    </svg>
                </div>
                <h3>الذكاء الاصطناعي</h3>
                <p>تحليل السير الذاتية وتقييم الأداء بالذكاء الاصطناعي</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                </div>
                <h3>التقارير واللوحات</h3>
                <p>لوحات تحكم تفاعلية وتقارير PDF</p>
            </div>

            <div class="feature-card animate-on-scroll">
                <div class="feature-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <h3>نظام الصلاحيات</h3>
                <p>5 لوحات تحكم منفصلة لكل دور وظيفي</p>
            </div>
        </div>
    </section>

    <section class="section" id="stats">
        <div class="section-title animate-on-scroll">
            <h2>بالأرقام</h2>
            <p>إحصائيات حية من نظامنا</p>
        </div>
        <div class="stats-grid">
            <div class="stat-card animate-on-scroll">
                <div class="stat-number" data-target="{{ $stats['employees'] }}">0</div>
                <div class="stat-label">الموظفين النشطين</div>
            </div>
            <div class="stat-card animate-on-scroll">
                <div class="stat-number" data-target="{{ $stats['projects'] }}">0</div>
                <div class="stat-label">المشاريع المنجزة</div>
            </div>
            <div class="stat-card animate-on-scroll">
                <div class="stat-number" data-target="{{ $stats['tasks_completed'] }}">0</div>
                <div class="stat-label">المهام المكتملة</div>
            </div>
            <div class="stat-card animate-on-scroll">
                <div class="stat-number" data-target="{{ $stats['clients'] }}">0</div>
                <div class="stat-label">العملاء</div>
            </div>
        </div>
    </section>

    <section class="section" id="panels">
        <div class="section-title animate-on-scroll">
            <h2>لوحات تحكم مخصصة لكل دور</h2>
            <p>كل دور وظيفي يحصل على لوحة تحكم خاصة به</p>
        </div>
        <div class="panels-grid">
            <div class="panel-card amber animate-on-scroll">
                <h3>لوحة المدير العام <span class="panel-dot"></span></h3>
                <p>إدارة شاملة للنظام والتحكم في جميع العمليات والإعدادات</p>
            </div>
            <div class="panel-card rose animate-on-scroll">
                <h3>الموارد البشرية <span class="panel-dot"></span></h3>
                <p>التوظيف والإجازات والمهارات وإدارة شؤون الموظفين</p>
            </div>
            <div class="panel-card blue animate-on-scroll">
                <h3>إدارة المشاريع <span class="panel-dot"></span></h3>
                <p>المشاريع والمهام وكانبان وتتبع التقدم والإنجاز</p>
            </div>
            <div class="panel-card emerald animate-on-scroll">
                <h3>المحاسب <span class="panel-dot"></span></h3>
                <p>الفواتير والمصروفات والرواتب والتقارير المالية</p>
            </div>
            <div class="panel-card indigo animate-on-scroll">
                <h3>لوحة الموظف <span class="panel-dot"></span></h3>
                <p>المهام والتقارير والإجازات والملف الشخصي</p>
            </div>
        </div>
    </section>

    <section class="cta-section" id="contact">
        <div class="cta-box animate-on-scroll">
            <h2>ابدأ رحلتك معنا اليوم</h2>
            <p>سجل الآن واحصل على تجربة مجانية</p>
            <a href="/register" class="btn-primary btn-lg">إنشاء حساب مجاني</a>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-copy">&copy; 2026 ERP Lite - نظام الإدارة المركزي</div>
            <div class="footer-links">
                <a href="#">سياسة الخصوصية</a>
                <a href="#">شروط الاستخدام</a>
                <a href="/lang/en">English</a>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            var navbar = document.getElementById('navbar');
            window.addEventListener('scroll', function () {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            var animatedEls = document.querySelectorAll('.animate-on-scroll');
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

            animatedEls.forEach(function (el) {
                observer.observe(el);
            });

            var statNumbers = document.querySelectorAll('.stat-number[data-target]');
            var statObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var el = entry.target;
                        var target = parseInt(el.getAttribute('data-target'), 10);
                        var duration = 1500;
                        var start = 0;
                        var startTime = null;

                        function animate(currentTime) {
                            if (!startTime) startTime = currentTime;
                            var progress = Math.min((currentTime - startTime) / duration, 1);
                            var eased = 1 - Math.pow(1 - progress, 3);
                            el.textContent = Math.floor(eased * target);
                            if (progress < 1) {
                                requestAnimationFrame(animate);
                            } else {
                                el.textContent = target;
                            }
                        }

                        requestAnimationFrame(animate);
                        statObserver.unobserve(el);
                    }
                });
            }, { threshold: 0.5 });

            statNumbers.forEach(function (el) {
                statObserver.observe(el);
            });
        })();
    </script>
</body>
</html>
