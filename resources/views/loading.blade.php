<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Analisis Kredit</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Menggunakan font Inter sebagai default */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
        /* Custom scrollbar untuk tampilan yang lebih baik */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        /* Style untuk input tanggal agar placeholder terlihat */
        input[type="date"]:before {
            content: attr(placeholder) !important;
            color: #aaa;
            margin-right: 0.5em;
        }
        input[type="date"]:focus:before,
        input[type="date"]:valid:before {
            content: "";
        }
    </style>
</head>
<body class="antialiased text-gray-800">

    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar (Opsional, untuk navigasi) -->
        <div class="hidden md:flex flex-col w-64 bg-white shadow-lg">
            <div class="flex items-center justify-center h-16 bg-white shadow-md">
                <svg class="h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span class="ml-2 text-xl font-bold text-gray-800">Analitik</span>
            </div>
            <div class="flex flex-col flex-grow p-4 overflow-auto">
                <a class="flex items-center px-4 py-2 mt-2 text-gray-100 bg-blue-600 rounded-lg" href="#">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <span class="ml-3">Dasbor</span>
                </a>
                <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-200 hover:text-gray-800 rounded-lg" href="#">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span class="ml-3">Nasabah</span>
                </a>
                <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-200 hover:text-gray-800 rounded-lg" href="#">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <span class="ml-3">Kredit</span>
                </a>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="flex flex-col flex-grow">
            <header class="flex items-center justify-between h-16 px-6 bg-white shadow-md">
                <h1 class="text-2xl font-semibold text-gray-800">Dasbor Analisis Kredit</h1>
                <div class="flex items-center">
                    <span class="text-sm mr-4">Selamat Datang, Admin!</span>
                    <img class="w-10 h-10 rounded-full object-cover" src="https://placehold.co/100x100/E2E8F0/4A5568?text=A" alt="Avatar Pengguna">
                </div>
            </header>

            <main class="flex-grow p-6 overflow-auto">
                <!-- Panel Filter -->
                <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-700">Filter Data</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Tanggal Awal -->
                        <div class="flex flex-col">
                            <label for="tanggal-awal" class="text-sm font-medium mb-1 text-gray-600">Tanggal Awal</label>
                            <input type="date" id="tanggal-awal" placeholder="Pilih tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <!-- Tanggal Akhir -->
                        <div class="flex flex-col">
                            <label for="tanggal-akhir" class="text-sm font-medium mb-1 text-gray-600">Tanggal Akhir</label>
                            <input type="date" id="tanggal-akhir" placeholder="Pilih tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <!-- Cabang -->
                        <div class="flex flex-col">
                            <label for="cabang" class="text-sm font-medium mb-1 text-gray-600">Cabang</label>
                            <select id="cabang" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Semua Cabang</option>
                                <option>Cabang Utama</option>
                                <option>Cabang Pembantu A</option>
                                <option>Cabang Pembantu B</option>
                            </select>
                        </div>
                        <!-- Jenis Kredit -->
                        <div class="flex flex-col">
                            <label for="jenis-kredit" class="text-sm font-medium mb-1 text-gray-600">Jenis Kredit</label>
                            <select id="jenis-kredit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Semua Jenis</option>
                                <option>KPR</option>
                                <option>KUR</option>
                                <option>KTA</option>
                            </select>
                        </div>
                        <!-- Nama Analis -->
                        <div class="flex flex-col">
                            <label for="nama-analis" class="text-sm font-medium mb-1 text-gray-600">Nama Analis</label>
                            <select id="nama-analis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Semua Analis</option>
                                <option>Budi Santoso</option>
                                <option>Citra Lestari</option>
                                <option>Doni Firmansyah</option>
                            </select>
                        </div>
                        <!-- Rincian Kredit -->
                        <div class="flex flex-col">
                            <label for="rincian-kredit" class="text-sm font-medium mb-1 text-gray-600">Rincian Kredit</label>
                            <select id="rincian-kredit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Semua Rincian</option>
                                <option>Lancar</option>
                                <option>Diragukan</option>
                                <option>Macet</option>
                            </select>
                        </div>
                        <!-- Tombol Filter -->
                        <div class="md:col-span-3 flex justify-end pt-2">
                             <button id="filter-button" class="w-full md:w-auto bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Area Grafik -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Grafik DPK -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-2">Grafik DPK (Dana Pihak Ketiga)</h3>
                        <canvas id="dpkChart"></canvas>
                    </div>
                    <!-- Grafik NPL -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-2">Grafik NPL (Non-Performing Loan)</h3>
                        <canvas id="nplChart"></canvas>
                    </div>
                    <!-- Grafik Nilai Wajar -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-2">Grafik Nilai Wajar Kredit</h3>
                        <canvas id="nilaiWajarChart"></canvas>
                    </div>
                </div>

                <!-- Area Tabel -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <!-- Tabel Nasabah DPK -->
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-4">Tabel Nasabah DPK Teratas</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">No</th>
                                        <th scope="col" class="px-6 py-3">Nama Nasabah</th>
                                        <th scope="col" class="px-6 py-3">No. Rekening</th>
                                        <th scope="col" class="px-6 py-3 text-right">Jumlah (Juta)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">1</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">PT. Sejahtera Abadi</td>
                                        <td class="px-6 py-4">1234567890</td>
                                        <td class="px-6 py-4 text-right">1,500</td>
                                    </tr>
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">2</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">CV. Maju Jaya</td>
                                        <td class="px-6 py-4">0987654321</td>
                                        <td class="px-6 py-4 text-right">980</td>
                                    </tr>
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">3</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">Andi Wijaya</td>
                                        <td class="px-6 py-4">1122334455</td>
                                        <td class="px-6 py-4 text-right">750</td>
                                    </tr>
                                     <tr class="bg-white">
                                        <td class="px-6 py-4">4</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">Siti Aminah</td>
                                        <td class="px-6 py-4">5566778899</td>
                                        <td class="px-6 py-4 text-right">620</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Tabel Nasabah NPL -->
                     <div class="bg-white p-6 rounded-xl shadow-lg">
                        <h3 class="text-lg font-semibold mb-4">Tabel Nasabah NPL Teratas</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">No</th>
                                        <th scope="col" class="px-6 py-3">Nama Nasabah</th>
                                        <th scope="col" class="px-6 py-3">Jenis Kredit</th>
                                        <th scope="col" class="px-6 py-3 text-right">Tunggakan (Juta)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">1</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">Toko Roti Enak</td>
                                        <td class="px-6 py-4">KUR</td>
                                        <td class="px-6 py-4 text-right text-red-600">250</td>
                                    </tr>
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">2</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">Bambang Susilo</td>
                                        <td class="px-6 py-4">KPR</td>
                                        <td class="px-6 py-4 text-right text-red-600">180</td>
                                    </tr>
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">3</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">PT. Cipta Karya</td>
                                        <td class="px-6 py-4">KTA</td>
                                        <td class="px-6 py-4 text-right text-red-600">155</td>
                                    </tr>
                                     <tr class="bg-white">
                                        <td class="px-6 py-4">4</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">Usaha Dagang Lancar</td>
                                        <td class="px-6 py-4">KUR</td>
                                        <td class="px-6 py-4 text-right text-red-600">95</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data dummy untuk grafik
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];

            // --- Inisialisasi Grafik DPK ---
            const dpkCtx = document.getElementById('dpkChart').getContext('2d');
            new Chart(dpkCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'DPK (Miliar Rp)',
                        data: [120, 150, 140, 180, 200, 210],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // --- Inisialisasi Grafik NPL ---
            const nplCtx = document.getElementById('nplChart').getContext('2d');
            new Chart(nplCtx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'NPL (%)',
                        data: [2.5, 2.4, 2.6, 2.5, 2.4, 1.8],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(255, 159, 64, 0.5)',
                            'rgba(255, 205, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(153, 102, 255, 0.5)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)',
                            'rgb(153, 102, 255)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + '%'
                                }
                            }
                        }
                    }
                }
            });

            // --- Inisialisasi Grafik Nilai Wajar ---
            const nilaiWajarCtx = document.getElementById('nilaiWajarChart').getContext('2d');
            new Chart(nilaiWajarCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Lancar', 'Diragukan', 'Macet'],
                    datasets: [{
                        label: 'Distribusi Nilai Wajar',
                        data: [300, 50, 25],
                        backgroundColor: [
                            'rgb(75, 192, 192)',
                            'rgb(255, 205, 86)',
                            'rgb(255, 99, 132)'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                }
            });

            // --- Fungsi untuk Tombol Filter ---
            const filterButton = document.getElementById('filter-button');
            filterButton.addEventListener('click', () => {
                // Di aplikasi nyata, di sini Anda akan mengambil data baru
                // berdasarkan filter dan memperbarui grafik serta tabel.
                // Untuk demo ini, kita hanya akan menampilkan alert.
                alert('Menerapkan filter... (Fungsi update data belum diimplementasikan)');
            });
        });
    </script>
</body>
</html>
