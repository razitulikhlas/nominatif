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
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
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

    #customer-table th, #customer-table td {
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

    /* CSS untuk Loading Screen */
    #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: none; /* Disembunyikan secara default */
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

        .spinner::before, .spinner::after {
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
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }

    /* @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    } */

    @media (max-width: 600px) {
        .container { padding: 20px; }
        h1 { font-size: 1.5em; }
    }
</style>
    <div class="content-wrapper" style="background-color: #f8f9fa;">


        {{-- Data Filter --}}
        <div class="row flex-grow p-3 overflow-auto" style="margin-top: -20px;">
        <form class="forms-sample" action="{{ route('dasboard.filter') }}" method="POST">
        @csrf
            <div class="bg-white p-6 rounded-xl mb-6 ">
                <h2 class="text-xl font-bold mb-4 text-gray-700">Filter Data2</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <!-- Tanggal Awal -->
                        <div class="flex flex-col">
                            <label for="tanggal-awal" class="text-sm font-medium mb-1 text-gray-600">Tanggal Awal</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" placeholder="Pilih tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <!-- Tanggal Akhir -->
                        <div class="flex flex-col">
                            <label for="tanggal-akhir" class="text-sm font-medium mb-1 text-gray-600">Tanggal Akhir</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" placeholder="Pilih tanggal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <!-- Cabang -->
                        <div class="flex flex-col">
                            <label for="cabang" class="text-sm font-medium mb-1 text-gray-600">Cabang</label>
                            <select id="cabang" name="cabang" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua</option>
                                @foreach ($cabang as $item)
                                <option value="{{$item->kode_cabang}}">{{ $item->nama_cabang}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Jenis Kredit -->
                        <div class="flex flex-col">
                            <label for="jenis_kredit" class="text-sm font-medium mb-1 text-gray-600">Jenis Kredit</label>
                            <select id="jenis_kredit" name="jenis_kredit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="1">Modal Kerja</option>
                                <option value="2">Konsumsi</option>
                            </select>
                        </div>
                        <!-- Nama Analis -->
                        <div class="flex flex-col">
                            <label for="kode_analis" class="text-sm font-medium mb-1 text-gray-600">Nama Analis</label>
                            <select id="kode_analis" name="kode_analis" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua</option>
                                @foreach ($analisKredit as $item)
                                <option value="{{$item->kode_analis}}">{{$item->nama_analis}}</option>
                                @endforeach

                            </select>
                        </div>
                        <!-- Rincian Kredit -->
                        <div class="flex flex-col">
                            <label for="rincian_kredit" class="text-sm font-medium mb-1 text-gray-600">Rincian Kredit</label>
                            <select id="rincian_kredit" name="rincian_kredit" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="1">KUR</option>
                                <option value="2">NON KUR</option>
                                <option value="3">KPR</option>
                                <option value="4">KCC</option>
                            </select>
                        </div>
                        <!-- Tombol Filter -->
                        <div class="md:col-span-3 flex justify-end pt-2">
                            <button id="filter-button" type="submit" class="w-full md:w-auto bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
                                Filter
                            </button>
                        </div>

                </div>
            </div>
        </form>
        </div>



        <div class="row">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Grafik DPK -->
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Grafik DPK (Dalam Perhatian khusus)</h3>
                    {!! $chartDPK->container() !!}
                </div>
                <!-- Grafik NPL -->
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Grafik NPL (Non-Performing Loan)</h3>
                    {!! $chartNPL->container() !!}
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">LANCAR,DPK,NPL</h3>
                    {!! $donut->container() !!}
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-6">
                <!-- Grafik Nilai Wajar -->
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold mb-2">Grafik Nilai Wajar</h3>
                    {!! $chartNilaiWajar->container() !!}
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
                        <div class="bulk-action-container">
                            <button id="sendMessage" class="send-all-btn" >
                                <i class="fab fa-whatsapp"></i> Kirim pesan tunggakan
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="nasabahTable" class="table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>KD CABANG</th>
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
                                <tbody>
                                    @forelse ($dataBlast as $item)
                                        {{-- Menggunakan @forelse --}}
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->KD_CAB }}</td>
                                            <td>{{ $item->NAMA_SINGKAT }}</td>
                                            <td>{{ number_format((float) ($item->NILAI_WAJAR ?? 0), 0, ',', '.') }}</td>
                                            <td>{{ $item->NO_REK }}</td>
                                            <td>{{ $item->NO_REK_AFILIASI }}</td>
                                            <td>{{ $item->NOHP }}</td>
                                            <td>{{ number_format((float) ($item->TOTAL_TUNGGAKAN ?? 0), 0, ',', '.') }}</td>
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




        {{-- NASABAH NPL --}}
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">NPL</h4>
                        <div class="table-responsive">
                            <table id="nasabahTableNPL" class="table">
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
        <p id="loading-text">Mengirim pesan...</p>
    </div>


    <script src="{{ $chartNPL->cdn() }}"></script>
    <script src="{{ $chartDPK->cdn() }}"></script>
    <script src="{{ $chartNilaiWajar->cdn() }}"></script>
    <script src="{{ $donut->cdn() }}"></script>



    {{ $chartNPL->script() }}
    {{ $chartDPK->script() }}
    {{ $donut->script() }}
    {{ $chartNilaiWajar->script() }}

    <script>
        $(function() {
            $('#nasabahTable').DataTable({
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

            $('#nasabahTableNPL').DataTable({
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

            $('#nasabahTableDPK').DataTable({
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

            // $('#filter-button').on('click', function() {
            //     // Logika untuk filter data berdasarkan inputan
            //     const tanggalAwal = $('#tanggal_awal').val();
            //     const tanggalAkhir = $('#tanggal_akhir').val();
            //     const cabang = $('#cabang').val();
            //     const jenisKredit = $('#jenis_kredit').val();
            //     const kodeAnalis = $('#kode_analis').val();
            //     const rincianKredit = $('#rincian_kredit').val();

            //     // Lakukan request AJAX atau filter data sesuai kebutuhan
            //     console.log('Filter diterapkan:', {
            //         tanggalAwal,
            //         tanggalAkhir,
            //         cabang,
            //         jenisKredit,
            //         kodeAnalis,
            //         rincianKredit
            //     });
            // });

            $('#sendMessage').on('click', function() {
                // Logika sisi klien (opsional)
                console.log('Tombol diklik, mengirim request dengan jQuery...');
                const loadingOverlay = document.getElementById('loading-overlay');

                // 1. Tampilkan loading screen dengan efek transisi
                loadingOverlay.classList.add('show');

                $.ajax({
                    url: '{{ route("dasboard.sendwa") }}', // URL dari named route
                    type: 'GET', // Metode request
                    data: {
                        // Data yang akan dikirim ke controller
                        info: 'Ini data dari client via jQuery',
                        user_id: 5
                    },
                    success: function(response) {
                        // 4. Jika request berhasil (success)
                        console.log('Respons dari server:', response);
                        const message = `<div class="alert alert-success">${response.message}</div>`;
                        $('#hasil').html(message);
                        loadingOverlay.classList.remove('show');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // 5. Jika terjadi error
                        console.error('Gagal melakukan request:', textStatus, errorThrown);
                        const message = `<div class="alert alert-danger">Terjadi kesalahan.</div>`;
                        $('#hasil').html(message);
                        loadingOverlay.classList.remove('show');
                    }
                });

            });

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
