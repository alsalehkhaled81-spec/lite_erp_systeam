<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('filament.auth.app_title') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Tajawal', 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 30%, #312e81 60%, #1e293b 100%);
            position: relative;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.35;
            animation: floatOrb 14s ease-in-out infinite;
        }
        body::before {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(99,102,241,0.25), transparent);
            top: -15%; left: -8%;
        }
        body::after {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(59,130,246,0.2), transparent);
            bottom: -15%; right: -8%;
            animation-delay: -7s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(25px, -25px) scale(1.03); }
            66% { transform: translate(-15px, 15px) scale(0.97); }
        }

        .auth-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 28px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.25), 0 0 120px rgba(99,102,241,0.04);
            animation: cardFadeIn 0.7s cubic-bezier(0.22, 1, 0.36, 1);
            color: #e2e8f0;
        }

        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(28px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .auth-card h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
        }

        .auth-card .subtitle {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.45);
            margin-bottom: 2rem;
        }

        .form-group { margin-bottom: 1.2rem; }

        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: rgba(255,255,255,0.6);
            margin-bottom: 0.4rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.92rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .form-group input::placeholder { color: rgba(255,255,255,0.25); }

        .form-group input:focus,
        .form-group select:focus {
            border-color: rgba(99,102,241,0.5);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
            background: rgba(255,255,255,0.08);
        }

        .form-group select option {
            background: #1e293b;
            color: #e2e8f0;
        }

        .error-text {
            color: #fca5a5;
            font-size: 0.75rem;
            margin-top: 0.35rem;
            display: block;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.2rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.55);
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            accent-color: #6366f1;
            width: 16px; height: 16px;
            border-radius: 4px;
        }

        .link {
            color: #818cf8;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .link:hover { color: #a5b4fc; }

        .btn-primary {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(99,102,241,0.25);
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99,102,241,0.35);
        }
        .btn-primary:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(99,102,241,0.2); }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 16px rgba(16,185,129,0.25);
        }
        .btn-success:hover {
            box-shadow: 0 8px 28px rgba(16,185,129,0.35);
        }

        .footer-text {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.4);
        }
        .footer-text a {
            color: #818cf8;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .footer-text a:hover { color: #a5b4fc; }

        .success-alert {
            padding: 0.85rem 1rem;
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 12px;
            color: #6ee7b7;
            font-size: 0.85rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.2rem;
        }

        @media (max-width: 480px) {
            .auth-card { margin: 1rem; padding: 1.75rem; border-radius: 20px; }
        }
    </style>
    @livewireStyles
</head>
<body>
    <div style="position: absolute; top: 1.5rem; right: {{ app()->getLocale() === 'ar' ? 'auto' : '1.5rem' }}; left: {{ app()->getLocale() === 'ar' ? '1.5rem' : 'auto' }}; z-index: 50;">
        <a href="{{ route('switch-language', ['locale' => app()->getLocale() === 'ar' ? 'en' : 'ar']) }}" 
           style="display: flex; items-center; gap: 0.5rem; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 0.5rem 1rem; border-radius: 20px; color: white; text-decoration: none; font-size: 0.9rem; font-weight: 600; border: 1px solid rgba(255,255,255,0.2); transition: all 0.2s ease;"
           onmouseover="this.style.background='rgba(255,255,255,0.2)'"
           onmouseout="this.style.background='rgba(255,255,255,0.1)'">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
        </a>
    </div>

    <div class="auth-card">
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
