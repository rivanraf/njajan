<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6">
            
            <div class="w-full sm:max-w-md bg-white shadow-sm overflow-hidden rounded-2xl">
                <div class="px-6 py-8 sm:px-10">
                    
                    <div class="mb-8 text-center">
                        <a href="/" class="inline-block mb-4">
                            <img src="{{ asset('images/logonjajan2.png') }}" 
                                 alt="Logo Njajan" 
                                 class="w-16 h-auto sm:w-20 object-contain mx-auto" />
                        </a>

                        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                            Welcome Back👋
                        </h2>
                        
                        <p class="mt-2 text-sm text-gray-500 leading-relaxed">
                            Enter your credentials to login to your account
                        </p>
                    </div>

                    <div class="space-y-6">
                        {{ $slot }}
                    </div>

                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-xs text-gray-400 font-medium tracking-wide uppercase">
                    &copy; {{ date('Y') }} Njajan++ Management System
                </p>
            </div>
        </div>
    </body>
</html>