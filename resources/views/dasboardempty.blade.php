@extends('layouts.main')
@section('content')

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kredit</title>
    <!-- Memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Memuat Font Inter dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Menggunakan font Inter sebagai default */
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Animasi untuk SVG agar lebih hidup */
        .interactive-svg .paper-stack {
            transition: transform 0.3s ease-in-out;
        }
        .interactive-svg:hover .paper-stack {
            transform: translateY(-8px) rotate(-3deg);
        }
        .interactive-svg .magnifying-glass {
            transition: transform 0.3s ease-in-out;
        }
        .interactive-svg:hover .magnifying-glass {
            transform: scale(1.1) rotate(5deg);
        }
    </style>
</head>
<body class="bg-gray-50">

    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Kredit</h1>

        <!-- {{-- Logika Blade: Ganti 'data_kredit' dengan variabel Anda --}}
        {{-- @if ($data_kredit->isEmpty()) --}} -->

        <!-- === BAGIAN KETIKA DATA KOSONG === -->
        <div class="text-center bg-white p-8 md:p-16 rounded-2xl shadow-sm border border-gray-200">
            <!-- Gambar Interaktif SVG -->
            <div class="interactive-svg w-48 h-48 mx-auto mb-6 cursor-pointer">
                 <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g class="paper-stack">
                        <path d="M62.5 150V62.5C62.5 59.1848 63.8165 56.0054 66.1612 53.6607C68.5058 51.316 71.6848 50 75 50H137.5L150 62.5V150C150 153.315 148.684 156.495 146.339 158.839C143.995 161.184 140.815 162.5 137.5 162.5H75C71.6848 162.5 68.5058 161.184 66.1612 158.839C63.8165 156.495 62.5 153.315 62.5 150Z" fill="#E0E7FF"/>
                        <path d="M50 137.5V50C50 46.6848 51.3165 43.5054 53.6612 41.1607C56.0058 38.816 59.1848 37.5 62.5 37.5H125L137.5 50V137.5C137.5 140.815 136.184 143.995 133.839 146.339C131.495 148.684 128.315 150 125 150H62.5C59.1848 150 56.0058 148.684 53.6612 146.339C51.3165 143.995 50 140.815 50 137.5Z" fill="#C7D2FE"/>
                        <path d="M81.25 75H118.75" stroke="#4F46E5" stroke-width="5" stroke-linecap="round"/>
                        <path d="M81.25 93.75H118.75" stroke="#4F46E5" stroke-width="5" stroke-linecap="round"/>
                        <path d="M81.25 112.5H100" stroke="#4F46E5" stroke-width="5" stroke-linecap="round"/>
                    </g>
                    <g class="magnifying-glass">
                        <circle cx="131.25" cy="68.75" r="25" fill="#6366F1" stroke="white" stroke-width="5"/>
                        <path d="M150 87.5L168.75 106.25" stroke="#6366F1" stroke-width="10" stroke-linecap="round"/>
                    </g>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Data Belum Ditemukan</h2>
            <p class="text-gray-500 mt-2 mb-6 max-w-md mx-auto">Sepertinya Anda belum memiliki data kredit. Silakan unggah data nominatif terlebih dahulu untuk memulai analisis.</p>

            <a href="/nominatif" {{-- Ganti dengan URL/route halaman nominatif Anda --}}
               class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                Input Data Nominatif
            </a>
        </div>
    </div>

</body>
</html>


@endsection
