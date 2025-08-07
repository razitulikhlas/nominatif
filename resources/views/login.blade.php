<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Modern</title>

    <!-- Memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Memuat Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Mengaplikasikan font Inter ke seluruh halaman */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900">

    <!-- Kontainer Utama dengan Latar Belakang Gradien -->
    <div class="relative min-h-screen w-full flex items-center justify-center bg-gradient-to-br from-slate-900 via-gray-900 to-blue-900 p-4">

        <!-- Efek blur di latar belakang -->
        <div class="absolute w-60 h-60 bg-sky-400 rounded-full -top-5 -left-16 mix-blend-lighten filter blur-3xl opacity-40 animate-blob"></div>
        <div class="absolute w-60 h-60 bg-pink-400 rounded-full -bottom-5 -right-10 mix-blend-lighten filter blur-3xl opacity-40 animate-blob animation-delay-2000"></div>
        <div class="absolute w-60 h-60 bg-yellow-400 rounded-full -bottom-20 left-20 mix-blend-lighten filter blur-3xl opacity-40 animate-blob animation-delay-4000"></div>

        <!-- Kartu Login dengan Efek Glassmorphism -->
        <div class="relative w-full max-w-md bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-8 md:p-10">
                <!-- Header Formulir -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-white">Selamat Datang Kembali</h1>
                    <p class="text-gray-300 mt-2">Silakan masuk untuk melanjutkan</p>
                </div>

                <!-- Formulir Login -->
                <form id="login-form" action="/login" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Input Email -->
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </span>
                            <input
                                type="username"
                                name="username"
                                placeholder="username"
                                required
                                class="w-full bg-white/10 text-white placeholder-gray-400 border-2 border-transparent focus:border-sky-500 rounded-full py-3 pl-12 pr-4 transition-colors duration-300 focus:outline-none"
                            >
                        </div>

                        <!-- Input Password -->
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password"
                                placeholder="Kata Sandi"
                                required
                                class="w-full bg-white/10 text-white placeholder-gray-400 border-2 border-transparent focus:border-sky-500 rounded-full py-3 pl-12 pr-4 transition-colors duration-300 focus:outline-none"
                            >
                        </div>
                    </div>

                    <!-- Lupa Password -->
                    {{-- <div class="text-right mt-4">
                        <a href="#" class="text-sm text-gray-400 hover:text-sky-400 transition-colors duration-300">Lupa Kata Sandi?</a>
                    </div> --}}

                    <!-- Tombol Masuk -->
                    <div class="mt-8">
                        <button
                            type="submit"
                            id="login-button"
                            class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center disabled:opacity-75 disabled:cursor-not-allowed"
                        >
                            <span id="button-text">Masuk</span>
                            <!-- Ikon Spinner (tersembunyi secara default) -->
                            <svg id="spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Link Daftar -->
                {{-- <div class="mt-8 text-center">
                    <p class="text-gray-400">
                        Belum punya akun?
                        <a href="#" class="font-semibold text-white hover:text-sky-400 transition-colors duration-300">Daftar di sini</a>
                    </p>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Script untuk Animasi Blob (Opsional) -->
    <style>
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
    </style>

    <!-- JavaScript untuk menangani logika loading -->
    {{-- <script>
        const loginForm = document.getElementById('login-form');
        const loginButton = document.getElementById('login-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');

        loginForm.addEventListener('submit', function(event) {
            // Mencegah pengiriman formulir standar untuk keperluan demo
            event.preventDefault();

            // Menampilkan status loading
            buttonText.classList.add('hidden');
            spinner.classList.remove('hidden');
            loginButton.disabled = true;

            // Mensimulasikan permintaan jaringan (proses login)
            setTimeout(() => {
                // Di aplikasi nyata, di sini Anda akan menangani respons dari server.
                // Untuk demo ini, kita hanya akan mengatur ulang tombol.

                // Menyembunyikan status loading
                buttonText.classList.remove('hidden');
                spinner.classList.add('hidden');
                loginButton.disabled = false;

                // Opsional: Tampilkan pesan berhasil/gagal
                // Sebaiknya gunakan notifikasi custom, bukan alert().

            }, 3000); // Penundaan 3 detik untuk demonstrasi
        });
    </script> --}}
</body>
</html>
