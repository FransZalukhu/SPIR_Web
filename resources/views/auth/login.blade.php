<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPIR</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="{{ asset('assets/spir_logo(ico).png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="font-poppins">
    <div
        class="min-h-screen bg-gray-100 bg-center bg-no-repeat flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Logo -->
        <div class="absolute top-6 right-6 sm:top-8 sm:right-8 md:top-10 md:right-10 lg:top-12 lg:left-12 z-20">
            <a href="/" class="block group relative inline-block">
                <div
                    class="absolute -inset-1 bg-customGreen/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-md">
                </div>
                <img src="{{ asset('assets/spir_logo.png') }}" alt="SPIR Logo"
                    class="w-20 sm:w-24 md:w-28 lg:w-32 h-auto relative transition-all duration-300 group-hover:scale-105 group-hover:brightness-110 rounded-lg z-10">
            </a>
        </div>

        <div
            class="max-w-md w-full space-y-8 bg-white/90 backdrop-blur-sm rounded-xl shadow-lg p-6 sm:p-8 lg:p-10 relative z-10">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-2xl sm:text-3xl font-bold text-customGreen mb-2">Masuk</h2>
                <p class="text-gray-500">Selamat datang di SPIR</p>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if (session('message') || session('error') || $errors->any())
            <div class="bg-red-100 border-l-4 border-red-400 p-4 mb-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        @if (session('message'))
                        <p class="text-sm text-red-700">{{ session('message') }}</p>
                        @elseif (session('error'))
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                        @elseif ($errors->any())
                        <ul class="list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-5">
                    <!-- Email Input -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" id="name" name="name"
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-300 text-gray-900 focus:outline-none focus:ring-customGreen focus:border-customGreen focus:z-10 sm:text-sm"
                            required value="{{ old('name') }}" placeholder="Masukkan Username">
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password"
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-300 text-gray-900 focus:outline-none focus:ring-customGreen focus:border-customGreen focus:z-10 sm:text-sm"
                            required placeholder="Minimal 8 Karakter">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-customGreen hover:bg-customGreenHover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-customGreen transition-colors duration-200">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>

</html>