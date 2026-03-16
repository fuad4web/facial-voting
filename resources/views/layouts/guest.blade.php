<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('auth/guest.css') }}">
    <link rel="stylesheet" href="{{ asset('auth/facial-login.css') }}">
</head>

<body>
    <div class="auth-wrapper">
        <!-- LEFT PANEL -->
        <div class="auth-left">
            <div class="auth-logo">
                <a href="/">
                    <x-application-logo style="width:60px;height:60px;fill:white"/>
                </a>
            </div>

            <h1>{{ config('app.name') }}</h1>

            <p>
                Manage your account securely and access your dashboard easily. Login or create an account to start exploring all the amazing features available on our platform.
            </p>
        </div>


        <!-- RIGHT PANEL -->
        <div class="auth-right">
            <div class="auth-card">

                {{ $slot }}

                <div class="auth-footer">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
