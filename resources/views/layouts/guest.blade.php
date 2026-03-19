<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('download.svg') }}">

    <title>{{ config('app.name', 'VeriVote') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('auth/guest.css') }}">
</head>
<body class="auth-page">
    <div class="auth-wrapper">
        <aside class="auth-left">
            <div class="auth-left-inner">
                <div class="auth-logo">
                    <a href="/">
                        <x-application-logo style="width:60px;height:60px;fill:white" />
                    </a>
                </div>

                <div class="auth-brand">
                    <span class="auth-brand-badge">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3a9 9 0 0 0-9 9v3"></path>
                            <path d="M21 12a9 9 0 0 1-9 9"></path>
                            <path d="M7.5 12a4.5 4.5 0 0 1 9 0"></path>
                            <path d="M9.5 12a2.5 2.5 0 0 1 5 0"></path>
                        </svg>
                    </span>

                    <div>
                        <h1>VeriVote</h1>
                        <p>Biometric voting made secure and simple</p>
                    </div>
                </div>

                <h2>Secure voting with fingerprint and facial recognition.</h2>

                <p class="auth-left-text">
                    VeriVote gives your voting system a clean and trusted identity with fast biometric
                    authentication, smooth access, and a professional experience for every user.
                </p>

                <div class="auth-feature-list">
                    <div class="auth-feature-item">
                        <span class="auth-dot"></span>
                        <span>Fingerprint login for verified access</span>
                    </div>
                    <div class="auth-feature-item">
                        <span class="auth-dot"></span>
                        <span>Facial recognition for extra protection</span>
                    </div>
                    <div class="auth-feature-item">
                        <span class="auth-dot"></span>
                        <span>Responsive design for mobile and desktop</span>
                    </div>
                </div>
            </div>

            <div class="auth-left-footer">
                <span>Trusted biometric voting platform</span>
                <span>&copy; {{ date('Y') }} VeriVote</span>
            </div>
        </aside>

        <main class="auth-right">
            <div class="auth-card">
                {{ $slot }}

                <div class="auth-footer">
                    &copy; {{ date('Y') }} {{ config('app.name', 'VeriVote') }}. All rights reserved.
                </div>
            </div>
        </main>
    </div>
</body>
</html>
