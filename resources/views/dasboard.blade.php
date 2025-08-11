@extends('layouts.main')
@section('content')
    <style>
        /* CSS untuk Tampilan Halaman */
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --background-color: #f4f7f6;
            --font-family: 'Poppins', sans-serif;
        }

        /* body {
                font-family: 'Inter', sans-serif;
            } */
        .card-icon-gradient {
            background-image: linear-gradient(135deg, var(--tw-gradient-from), var(--tw-gradient-to));
        }

        body {
            font-family: var(--font-family);
            background-color: var(--background-color);
            color: var(--dark-color);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            text-align: center;
            color: var(--dark-color);
            margin-bottom: 15px;
            font-weight: 700;
        }

        h1 .fa-triangle-exclamation {
            margin-right: 12px;
            color: var(--primary-color);
        }

        .bulk-action-container {
            text-align: left;
        }

        .send-all-btn {
            background: linear-gradient(45deg, var(--danger-color), #ff7e5f);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }

        .increase {
            color: var(--color-increase);
            background-color: rgba(25, 135, 84, 0.1);
        }

        .filter-all-btn {
            background: linear-gradient(45deg, var(--danger-color), #2839ed);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }

        .send-all-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(220, 53, 69, 0.5);
        }

        .send-all-btn:disabled {
            background: var(--secondary-color);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        #customer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #customer-table th,
        #customer-table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
            vertical-align: middle;
        }

        #customer-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #customer-table tbody tr:nth-child(even) {
            background-color: var(--light-color);
        }

        #customer-table tbody tr:hover {
            background-color: #e9ecef;
        }

        .card-percentage {
            font-size: 0.9rem;
            font-weight: 700;
            padding: 0.3rem 0.6rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* CSS untuk Loading Screen */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: none;
            /* Disembunyikan secara default */
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            backdrop-filter: blur(5px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #loading-overlay.show {
            display: flex;
            opacity: 1;
        }

        #loading-text {
            margin-top: 25px;
            font-size: 1.3em;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: 0.5px;
            animation: pulse 2s infinite ease-in-out;
        }

        /* Animasi Spinner yang lebih menarik */
        .spinner {
            width: 70px;
            height: 70px;
            position: relative;
        }

        .spinner::before,
        .spinner::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 6px solid transparent;
            border-top-color: var(--primary-color);
            animation: spin 1.5s linear infinite;
        }

        .spinner::after {
            border-top-color: var(--whatsapp-color);
            animation-delay: 0.5s;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.7;
            }
        }

        /* @keyframes spin {
                                                        0% { transform: rotate(0deg); }
                                                        100% { transform: rotate(360deg); }
                                                    } */

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 1.5em;
            }
        }
    </style>
    <div class="content-wrapper" style="background-color: #f8f9fa;">


        {{-- Data Filter --}}
        <div class="row flex-grow p-3 overflow-auto" style="margin-top: -20px;">
            {{-- <form class="forms-sample" action="{{ route('dasboard.filter') }}" method="POST"> --}}
            {{-- @csrf --}}
            <div class="bg-white p-6 rounded-xl mb-6 ">
                <h2 class="text-xl font-bold mb-4 text-gray-700">Filter Data</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- Tanggal Awal -->
                    <div class="flex flex-col">
                        <label for="tanggal-awal" class="text-sm font-medium mb-1 text-gray-600">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" id="tanggal_awal" placeholder="Pilih tanggal"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <!-- Tanggal Akhir -->
                    <div class="flex flex-col">
                        <label for="tanggal-akhir" class="text-sm font-medium mb-1 text-gray-600">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" placeholder="Pilih tanggal"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <!-- Cabang -->
                    @if (Auth::user()->rules == 1 || Auth::user()->rules == 0)
                        <div class="flex flex-col">
                            <label for="cabang" class="text-sm font-medium mb-1 text-gray-600">Cabang</label>
                            <select id="cabang" name="cabang"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua</option>
                                @if ($cabang)
                                    @foreach ($cabang as $item)
                                        <option value="{{ $item->kode_capem }}">{{ $item->nama_capem }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @endif

                    <!-- Jenis Kredit -->
                    <div class="flex flex-col">
                        <label for="jenis_kredit" class="text-sm font-medium mb-1 text-gray-600">Jenis Kredit</label>
                        <select id="jenis_kredit" name="jenis_kredit"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="1">Produktif</option>
                            <option value="2">Konsumtif</option>
                        </select>
                    </div>
                    <!-- Nama Analis -->
                    @if (Auth::user()->rules < 2)
                        <div class="flex flex-col">
                            <label for="kode_analis" class="text-sm font-medium mb-1 text-gray-600">Nama Analis</label>
                            <select id="kode_analis" name="kode_analis"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua</option>
                                @if ($analisKredit)
                                    @foreach ($analisKredit as $item)
                                        <option value="{{ $item->kode_analis }}">{{ $item->nama_analis }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @endif
                    <!-- Rincian Kredit -->
                    <div class="flex flex-col">
                        <label for="rincian_kredit" class="text-sm font-medium mb-1 text-gray-600">Rincian Kredit</label>
                        <select id="rincian_kredit" name="rincian_kredit"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="1">KUR</option>
                            <option value="2">NON KUR</option>
                            <option value="3">KPR</option>
                            <option value="4">KCC</option>
                        </select>
                    </div>
                    <!-- Tombol Filter -->
                    <div class="md:col-span-3 flex justify-end pt-2">
                        {{-- <button id="filter-button" type="submit" class="w-full md:w-auto bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
                                Filter
                            </button> --}}
                        <button id="filter" class="filter-all-btn">
                            <i class="fab fa-filter"></i> Filter
                        </button>
                    </div>

                </div>
            </div>
            {{-- </form> --}}
        </div>

        <!-- Grid untuk Kartu -->
        <div class="grid grid-cols-3 lg:grid-cols-2 gap-6 mb-6">

            <!-- Card 1: Nilai DPK -->
            <div
                class="bg-white p-6 rounded-2xl shadow-md border border-slate-200 flex items-center space-x-5 transition-transform duration-300 hover:-translate-y-1">
                <div
                    class="card-icon-gradient from-blue-500 to-blue-400 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-money-bill-wave text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-500 text-sm font-medium uppercase">DPK</p>
                    <p class="text-slate-800 text-3xl font-bold" id="infoNilaiDPK">Rp {{ $infoDPK }}</p>
                </div>
            </div>

            <!-- Card 2: Nilai NPL -->
            <div
                class="bg-white p-6 rounded-2xl shadow-md border border-slate-200 flex items-center space-x-5 transition-transform duration-300 hover:-translate-y-1">
                <div
                    class="card-icon-gradient from-red-500 to-red-400 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-500 text-sm font-medium uppercase">NPL</p>
                    <p class="text-slate-800 text-3xl font-bold" id="infoNilaiNPL">Rp {{ $infoNPL }}</p>
                </div>
            </div>

            <!-- Card 3: Nilai Wajar -->
            <div
                class="bg-white p-6 rounded-2xl shadow-md border border-slate-200 flex items-center space-x-5 transition-transform duration-300 hover:-translate-y-1">
                <div
                    class="card-icon-gradient from-emerald-500 to-emerald-400 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-scale-balanced text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-500 text-sm font-medium uppercase">Nilai Wajar</p>
                    <p class="text-slate-800 text-3xl font-bold" id="infoNilaiWajar">Rp {{ $infoNilaiWajar }}</p>
                </div>
            </div>

            <!-- Card 4: Penurunan Nilai Wajar -->
            <div
                class="bg-white p-6 rounded-2xl shadow-md border border-slate-200 flex items-center space-x-5 transition-transform duration-300 hover:-translate-y-1">
                <div
                    class="card-icon-gradient from-amber-500 to-amber-400 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-arrow-down-wide-short text-2xl"></i>
                </div>
                <div>
                    <p class="text-slate-500 text-sm font-medium uppercase">Penurunan Nilai Wajar</p>
                    <p class="text-slate-800 text-3xl font-bold" id="infoPenurunanNilaiWajar">Rp {{ $infoTurunNilaiWajar }}
                    </p>
                </div>
            </div>

        </div>


        <div class="row">
            <div class="grid grid-cols-3 lg:grid-cols-2 gap-6 mb-6">
                <!-- Grafik DPK -->
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Grafik DPK (Dalam Perhatian khusus)</h3>
                    <canvas id="chartDPK"></canvas>
                    {{-- {!! $chartDPK->container() !!} --}}
                </div>
                <!-- Grafik NPL -->
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Grafik NPL (Non-Performing Loan)</h3>
                    <canvas id="chartNPL"></canvas>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Grafik Nilai Wajar</h3>
                    <div class="relative h-72">
                        <canvas id="chartNilaiWajar"></canvas>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">LANCAR,DPK,NPL (dalam persentase)</h3>
                    <div class="relative h-72">
                        <canvas id="grafikDPK_NPL_LANCAR"></canvas>
                    </div>
                    {{-- {!! $donut->container() !!} --}}
                </div>
            </div>
        </div>

        {{-- NASABAH Whatsaap Blast --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h1>📊 Data Tunggakan Nasabah</h1>
                        {{-- Tombol send wa --}}
                        <div class="bulk-action-container mb-4">
                            <button id="sendMessage" class="send-all-btn">
                                <i class="fab fa-whatsapp"></i> Kirim pesan tunggakan
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="nasabahTable" class="table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>KD CABANG</th>
                                        <th>PETUGAS</th>
                                        <th>NAMA</th>
                                        <th>BAKI DEBET</th>
                                        <th>NOREK KREDIT</th>
                                        <th>NOREK AFILIASI</th>
                                        <th>No HP</th>
                                        <th>TOTAL TUNGGAKAN</th>
                                        <th>KOLEKTIBILITY</th>
                                        <th>POSISI</th>
                                    </tr>
                                </thead>
                                <tbody id="tblBodyTunggakan">
                                    @if ($dataBlast)
                                        {{-- @forelse digunakan untuk menampilkan data jika ada, jika tidak ada akan menampilkan pesan "Belum ada data" --}}
                                        @foreach ($dataBlast as $item)
                                            {{-- Menggunakan @forelse --}}
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->KD_CAB }}</td>
                                                <td>{{ $item->nama_analis }}</td>
                                                <td>{{ $item->NAMA_SINGKAT }}</td>
                                                <td>{{ number_format((float) ($item->NILAI_WAJAR ?? 0), 0, ',', '.') }}
                                                </td>
                                                <td>{{ $item->NO_REK }}</td>
                                                <td>{{ $item->NO_REK_AFILIASI }}</td>
                                                <td>{{ $item->NOHP }}</td>
                                                <td>{{ number_format((float) ($item->total_tunggakan ?? 0), 0, ',', '.') }}
                                                </td>
                                                <td>{{ $item->KOLEKTIBILITY }}</td>
                                                <td>{{ $item->TANGGAL }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        {{-- NASABAH NPL --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">NPL</h4>
                        <div class="table-responsive">
                            <table class="table" id="nasabahTableNPL">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>KD CABANG</th>
                                        <th>NAMA</th>
                                        <th>NOREK</th>
                                        <th>NILAI WAJAR</th>
                                        <th>KOLEKTIBILITY</th>
                                        <th>POSISI</th>
                                    </tr>
                                </thead>
                                <tbody id="tblBodyNPL">
                                    {{-- @forelse digunakan untuk menampilkan data jika ada, jika tidak ada akan menampilkan pesan "Belum ada data" --}}
                                    @forelse ($dataNBNPL as $item)
                                        {{-- Menggunakan @forelse --}}
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->KD_CAB }}</td>
                                            <td>{{ $item->NAMA_SINGKAT }}</td>
                                            <td>{{ $item->NO_REK }}</td>
                                            <td>{{ number_format((float) ($item->NILAI_WAJAR ?? 0), 0, ',', '.') }}</td>
                                            <td>{{ $item->KOLEKTIBILITY }}</td>
                                            <td>{{ $item->TANGGAL }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- NASABAH DPK --}}

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">DPK</h4>
                        <div class="table-responsive">
                            <table id="nasabahTableDPK" class="table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>KD CABANG</th>
                                        <th>NAMA</th>
                                        <th>NOREK</th>
                                        <th>NILAI WAJAR</th>
                                        <th>KOLEKTIBILITY</th>
                                        <th>POSISI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dataNBDPK as $item)
                                        {{-- Menggunakan @forelse --}}
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->KD_CAB }}</td>
                                            <td>{{ $item->NAMA_SINGKAT }}</td>
                                            <td>{{ $item->NO_REK }}</td>
                                            <td>{{ number_format((float) ($item->NILAI_WAJAR ?? 0), 0, ',', '.') }}</td>
                                            <td>{{ $item->KOLEKTIBILITY }}</td>
                                            <td>{{ $item->TANGGAL }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Screen Overlay -->
    <div id="loading-overlay">
        <div class="spinner"></div>
        <p id="loading-text">Memproses Data...</p>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        $(function() {
            const tableBody = $('#tblBodyNPL');

            var tableTunggakan = $('#nasabahTable').DataTable({
                "paging": true, // Aktifkan pagination (default: true)
                "searching": true, // Aktifkan pencarian (default: true)
                "ordering": false, // Aktifkan sorting (default: true)
                "info": false, // Tampilkan informasi (default: true
                "buttons": [ // Tambahkan tombol-tombol untuk ekspor data
                    'excel',
                    // 'pdf', 'print','copy', 'csv',
                ],
                "lengthChange": true, // Tampilkan opsi untuk mengubah jumlah entri yang ditampilkan (default: true)
                "language": { // Kustomisasi bahasa (contoh untuk Bahasa Indonesia)
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(difilter dari _MAX_ total entri)",
                    "zeroRecords": "Tidak ada data yang cocok ditemukan",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                },
                dom: 'Bfrtip', // Menentukan tata letak elemen DataTables (B: Buttons, f: filtering, r: processing, t: table, i: info, p: pagination)

            });

            var tableDPK = $('#nasabahTableDPK').DataTable({
                "paging": true, // Aktifkan pagination (default: true)
                "searching": true, // Aktifkan pencarian (default: true)
                "ordering": false, // Aktifkan sorting (default: true)
                "info": false, // Tampilkan informasi (default: true
                "buttons": [ // Tambahkan tombol-tombol untuk ekspor data
                    // 'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                "lengthChange": true, // Tampilkan opsi untuk mengubah jumlah entri yang ditampilkan (default: true)
                "language": { // Kustomisasi bahasa (contoh untuk Bahasa Indonesia)
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(difilter dari _MAX_ total entri)",
                    "zeroRecords": "Tidak ada data yang cocok ditemukan",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                },
                dom: 'Bfrtip', // Menentukan tata letak elemen DataTables (B: Buttons, f: filtering, r: processing, t: table, i: info, p: pagination)

            });

            var tableNPL = $('#nasabahTableNPL').DataTable({
                "paging": true, // Aktifkan pagination (default: true)
                "searching": true, // Aktifkan pencarian (default: true)
                "ordering": false, // Aktifkan sorting (default: true)
                "info": false, // Tampilkan informasi (default: true
                "buttons": [ // Tambahkan tombol-tombol untuk ekspor data
                    // 'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                "lengthChange": true, // Tampilkan opsi untuk mengubah jumlah entri yang ditampilkan (default: true)
                "language": { // Kustomisasi bahasa (contoh untuk Bahasa Indonesia)
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(difilter dari _MAX_ total entri)",
                    "zeroRecords": "Tidak ada data yang cocok ditemukan",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                },
                dom: 'Bfrtip',
            });

            $('#filter').on('click', function() {
                // Logika sisi klien (opsional)
                const loadingOverlay = document.getElementById('loading-overlay');

                // 1. Tampilkan loading screen dengan efek transisi
                loadingOverlay.classList.add('show');
                $.ajax({
                    url: '{{ route('testa') }}', // URL dari named route
                    type: 'GET', // Metode request
                    data: {
                        // Data yang akan dikirim ke controller
                        "tanggal_awal": $('#tanggal_awal').val(),
                        "tanggal_akhir": $('#tanggal_akhir').val(),
                        "cabang": $('#cabang').val(),
                        "jenis_kredit": $('#jenis_kredit').val(),
                        "kode_analis": $('#kode_analis').val(),
                        "rincian_kredit": $('#rincian_kredit').val(),
                    },
                    success: function(response) {
                        loadingOverlay.classList.remove('show');
                        // return console.log('Respons dari server:', response.danpl);
                        $('#infoNilaiWajar').text(response.infoNilaiWajar);
                        $('#infoNilaiDPK').text(response.infoDPK);
                        $('#infoNilaiNPL').text(response.infoNPL);
                        $('#infoPenurunanNilaiWajar').text(response.infoTurunNilaiWajar);
                        if (response.dataNBNPL && response.dataNBNPL.length > 0) {
                            tableNPL.clear();
                            var dataSetNPL = response.dataNBNPL.map(function(item, index) {
                                return [
                                    index + 1,
                                    item.KD_CAB,
                                    item.NAMA_SINGKAT,
                                    item.NO_REK,
                                    // Format angka di sisi klien (lebih disarankan)
                                    new Intl.NumberFormat('id-ID').format(item
                                        .NILAI_WAJAR),
                                    item.KOLEKTIBILITY,
                                    item.TANGGAL
                                ];
                            });

                            tableNPL.rows.add(dataSetNPL);
                            tableNPL.draw();

                        } else {
                            tableDPK.clear();
                            tableDPK.draw();
                        }

                        if (response.dataNBDPK && response.dataNBDPK.length > 0) {
                            tableDPK.clear();
                            var dataSetDPK = response.dataNBDPK.map(function(item, index) {
                                return [
                                    index + 1,
                                    item.KD_CAB,
                                    item.NAMA_SINGKAT,
                                    item.NO_REK,
                                    // Format angka di sisi klien (lebih disarankan)
                                    new Intl.NumberFormat('id-ID').format(item
                                        .NILAI_WAJAR),
                                    item.KOLEKTIBILITY,
                                    item.TANGGAL
                                ];
                            });

                            tableDPK.rows.add(dataSetDPK);
                            tableDPK.draw();
                        } else {
                            tableDPK.clear();
                            tableDPK.draw();
                        }


                        if (response.dataBlast && response.dataBlast.length > 0) {
                            tableTunggakan.clear();
                            dataTungakan = response.dataBlast;
                            var dataSetTunggakan = response.dataBlast.map(function(item,
                                index) {
                                return [
                                    index + 1,
                                    item.KD_CAB,
                                    item.nama_analis,
                                    item.NAMA_SINGKAT,
                                    new Intl.NumberFormat('id-ID').format(item
                                        .NILAI_WAJAR),
                                    item.NO_REK,
                                    item.NO_REK_AFILIASI,
                                    item.NOHP,
                                    new Intl.NumberFormat('id-ID').format(item
                                        .total_tunggakan),
                                    item.KOLEKTIBILITY,
                                    item.TANGGAL,
                                ];
                            });

                            tableTunggakan.rows.add(dataSetTunggakan);
                            tableTunggakan.draw();
                        } else {
                            tableTunggakan.clear();
                            tableTunggakan.draw();
                        }

                        if (chartNPL) {
                            chartNPL.destroy();
                        }
                        if (chartDP) {
                            chartDP.destroy();
                        }
                        if (chartNL) {
                            chartNL.destroy();
                        }

                        if (chartDPK_NPL_LANCAR) {
                            chartDPK_NPL_LANCAR.destroy();
                        }
                        chartNL = new Chart(cnl, {
                            type: 'line',
                            data: {
                                labels: (response.tanggal),
                                datasets: [{
                                    label: "Nilai Wajar",
                                    data: (response.gnilaiwajar),
                                    borderColor: "green",
                                    borderWidth: 3

                                }],
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false, // <-- TERAPKAN DI SINI JUGA
                                    // ...opsi lainnya
                                }
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        chartDP = new Chart(cdp, {
                            type: 'line',
                            data: {
                                labels: (response.tanggal),
                                datasets: [{
                                    label: "DPK",
                                    data: (response.gnilaidpk),
                                    // fill: true,
                                    borderWidth: 3,

                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        chartNPL = new Chart(cnp, {
                            type: 'line',
                            data: {
                                labels: (response.tanggal),
                                datasets: [{
                                    label: "NPL",
                                    data: (response.gnilainpl),
                                    borderColor: "red",
                                    borderWidth: 3

                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        chartDPK_NPL_LANCAR = new Chart(dnl, {
                            type: 'doughnut',
                            data: {
                                labels: (response.donutL),
                                datasets: [{
                                    label: "Prsentase",
                                    data: (response.donutD),
                                    backgroundColor: [
                                        'rgb(0,100,0)',
                                        'rgb(54, 162, 235)',
                                        'rgb(255, 205, 86)'
                                    ],
                                    // fill: true,
                                    borderWidth: 3,

                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                responsive: true,
                                maintainAspectRatio: false, // <-- TERAPKAN DI SINI JUGA
                                // ...opsi lainnya
                            }
                        });
                        // 4. Jika request berhasil (success)
                        console.log('Respons dari server:', response);
                        loadingOverlay.classList.remove('show');
                        // const message = `<div class="alert alert-success">${response.message}</div>`;
                        // $('#hasil').html(message);
                        // loadingOverlay.classList.remove('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // 5. Jika terjadi error
                        loadingOverlay.classList.remove('show');
                        console.error('Gagal melakukan request:', textStatus, errorThrown);
                        const message =
                            `<div class="alert alert-danger">Terjadi kesalahan.</div>`;
                        $('#hasil').html(message);
                        loadingOverlay.classList.remove('show');
                    }
                });

            });

            $('#sendMessage').on('click', function() {
                // Logika sisi klien (opsional)
                console.log('Tombol diklik, mengirim request dengan jQuery...');
                const loadingOverlay = document.getElementById('loading-overlay');

                // 1. Tampilkan loading screen dengan efek transisi
                loadingOverlay.classList.add('show');

                $.ajax({
                    url: '{{ route('dasboard.sendwa') }}', // URL dari named route
                    type: 'get', // Metode request
                    data: {
                        // Data yang akan dikirim ke controller
                        // "dataTungakan": dataTungakan,
                        "tanggal_awal": $('#tanggal_awal').val(),
                        "tanggal_akhir": $('#tanggal_akhir').val(),
                        "cabang": $('#cabang').val(),
                        "jenis_kredit": $('#jenis_kredit').val(),
                        "kode_analis": $('#kode_analis').val(),
                        "rincian_kredit": $('#rincian_kredit').val(),
                    },
                    success: function(response) {
                        loadingOverlay.classList.remove('show');
                        // 4. Jika request berhasil (success)
                        console.log('Respons dari server:', response);
                        // const message = `<div class="alert alert-success">${response.message}</div>`;
                        // $('#hasil').html(message);
                        // loadingOverlay.classList.remove('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // 5. Jika terjadi error
                        console.error('Gagal melakukan request:', textStatus, errorThrown);
                        const message =
                            `<div class="alert alert-danger">Terjadi kesalahan.</div>`;
                        $('#hasil').html(message);
                        loadingOverlay.classList.remove('show');
                    }
                });

            });




        });
    </script>

    <script>
        let chartNL;
        let chartDP;
        let chartNPL;
        let chartDPK_NPL_LANCAR;
        let dataTungakan = @json($dataBlast);;

        const cnl = document.getElementById('chartNilaiWajar');
        const cdp = document.getElementById('chartDPK');
        const cnp = document.getElementById('chartNPL');
        const dnl = document.getElementById('grafikDPK_NPL_LANCAR');



        console.log();

        chartNL = new Chart(cnl, {
            type: 'line',
            data: {
                labels: @json($tanggal),
                datasets: [{
                    label: "Nilai Wajar",
                    data: @json($gnilaiwajar),
                    borderColor: "green",
                    borderWidth: 3

                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        chartDP = new Chart(cdp, {
            type: 'line',
            data: {
                labels: @json($tanggal),
                datasets: [{
                    label: "DPK",
                    data: @json($gnilaidpk),
                    // fill: true,
                    borderWidth: 3,

                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        chartNPL = new Chart(cnp, {
            type: 'line',
            data: {
                labels: @json($tanggal),
                datasets: [{
                    label: "NPL",
                    data: @json($gnilainpl),
                    borderColor: "red",
                    borderWidth: 3

                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        chartDPK_NPL_LANCAR = new Chart(dnl, {
            type: 'doughnut',
            data: {
                labels: @json($donutL),
                datasets: [{
                    label: "Prsentase",
                    data: @json($donutD),
                    backgroundColor: [
                        'rgb(0,100,0)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)'
                    ],
                    // fill: true,
                    borderWidth: 3,

                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    </script>

    <script>
        function kirimPesan() {
            const loadingOverlay = document.getElementById('loading-overlay');

            // 1. Tampilkan loading screen dengan efek transisi
            loadingOverlay.classList.add('show');

            // 3. Beri jeda 2.5 detik agar loading screen terlihat,
            //    lalu buka link & sembunyikan loading screen.
            // setTimeout(() => {
            //     window.open(linkWA, '_blank'); // Buka WhatsApp di tab baru
            //     loadingOverlay.classList.remove('show'); // Sembunyikan loading screen
            // }, 2500);
        }
    </script>
@endsection
