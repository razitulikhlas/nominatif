@extends('layouts.main')
@section('content')
    <div class="content-wrapper">

        {{-- Contoh Alert (opsional, bisa dihapus) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- Akhir Contoh Alert --}}



        <div class="row">
            <div class="col-md grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        {{-- Menggunakan row dan col untuk layout judul dan tombol --}}
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-10">
                                <h4 class="card-title mb-0">Buat Surat Teguran,Peringatan 1,2,dan 3</h4>
                                {{-- Judul lebih deskriptif --}}
                            </div>
                            <div class="col-md-2 text-end">
                                {{-- Menambahkan data-bs-toggle="modal" --}}
                                @if ($cek < 4)
                                    <button type="button" class="btn btn-gradient-info btn-rounded btn-fw"
                                        data-bs-toggle="modal" data-bs-target="#myModal">
                                        <i class="mdi mdi-plus"></i> Tambah
                                    </button>
                                    @endif
                            </div>
                        </div>

                        {{-- Menambahkan div table-responsive untuk tabel --}}
                        <div class="table-responsive">
                            <table class="table table-hover"> {{-- Menambahkan class table-hover (opsional) --}}
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Surat</th>
                                        <th>Tanggal Surat</th>
                                        <th>Jenis Surat</th>
                                        <th>Alamat</th>
                                        <th>Tunggakan Pokok</th>
                                        <th>Tunggakan Bunga</th>
                                        <th>Denda Pokok</th>
                                        <th>Denda Bunga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($surat as $item)
                                        {{-- Menggunakan @forelse --}}
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nomor_surat }}</td>
                                            <td>{{ $item->tanggal_surat }}</td>
                                            <td>
                                                @if ($item->jenis_surat == 0)
                                                    Surat Teguran
                                                @elseif ($item->jenis_surat == 1)
                                                    Surat Peringatan 1
                                                @elseif ($item->jenis_surat == 2)
                                                    Surat Peringatan 2
                                                @elseif ($item->jenis_surat == 3)
                                                    Surat Peringatan 3
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->tunggakan_pokok, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->tunggakan_bunga, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->denda_pokok, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->denda_bunga, 2, ',', '.') }}</td>

                                            <td>
                                                {{-- Tombol Edit --}}
                                                <a href="/pdf/{{ $item->id }}"
                                                    class="btn btn-gradient-primary btn-sm p-1 edit-button">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-gradient-danger btn-sm p-1 edit-button"
                                                    data-bs-toggle="modal" data-id-surat={{ $item->id }}
                                                    data-bs-target="#exampleModalCenter" id="delete">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                                </a>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Belum ada data nomor WhatsApp.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- Akhir table-responsive --}}

                    </div> {{-- Akhir card-body --}}
                </div> {{-- Akhir card --}}
            </div> {{-- Akhir col-md --}}
        </div> {{-- Akhir row --}}


        <div class="row">
            <div class="col-md grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-10">
                                <h4 class="card-title mb-0">Detail Nasabah:
                                    {{ $nasabah->NAMA_SINGKAT ?? 'Tidak Diketahui' }}</h4>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">Kembali</a>
                            </div>
                        </div>

                        @if (isset($nasabah) && $nasabah)
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Informasi Pribadi</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 30%;">No. Rekening</th>
                                            <td style="width: 70%;">: {{ $nasabah->NO_REK ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Lengkap</th>
                                            <td>: {{ $nasabah->NAMA_SINGKAT ?? '-' }}</td> {{-- Ganti dengan NAMA_LENGKAP jika ada --}}
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td>: {{ $nasabah->NIK ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>NPWP</th>
                                            <td>: {{ $nasabah->NPWP ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. HP</th>
                                            <td>: {{ $nasabah->NOHP ?? '-' }}</td>
                                        </tr>
                                        {{-- Tambahkan field pribadi lainnya --}}
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Informasi Kredit</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 30%;">Kode Cabang</th>
                                            <td style="width: 70%;">: {{ $nasabah->KD_CAB ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Kredit</th>
                                            <td>: {{ $nasabah->PRD_NAME ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Plafond</th>
                                            <td>: Rp {{ number_format((float) ($nasabah->PLAFOND ?? 0), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Baki Debet</th>
                                            <td>: Rp {{ number_format((float) ($nasabah->SALDO_AKHIR ?? 0), 0, ',', '.') }}
                                            </td> {{-- Asumsi SALDO_AKHIR untuk baki debet --}}
                                        </tr>
                                        <tr>
                                            <th>Tanggal Jatuh Tempo</th>
                                            <td>: </td>
                                        </tr>
                                        <tr>
                                            <th>Kolektibilitas</th>
                                            <td>: {{ $nasabah->KOLEKTIBILITY ?? '-' }}</td>
                                        </tr>
                                        {{-- Tambahkan field kredit lainnya --}}
                                    </table>
                                </div>
                            </div>
                            <hr>
                            {{-- Anda bisa menambahkan bagian lain seperti riwayat pembayaran, detail agunan, dll. --}}

                            {{-- <div class="mt-3">
                                <a href="{{ route('pdf.show', ['norek' => $nasabah->NO_REK]) }}"
                                    class="btn btn-gradient-info btn-sm" target="_blank">
                                    <i class="mdi mdi-file-pdf"></i> Lihat Surat Teguran
                                </a>
                                {{-- Tombol aksi lain jika diperlukan --}}
                            {{-- </div> --}}
                        @else
                            <p class="text-center">Data nasabah tidak ditemukan.</p>
                        @endif

                    </div> {{-- Akhir card-body --}}
                </div> {{-- Akhir card --}}
            </div> {{-- Akhir col-md --}}
        </div> {{-- Akhir row --}}

    </div> {{-- Akhir content-wrapper --}}

    {{-- Modal dan script tidak diperlukan untuk halaman detail statis ini, kecuali jika ada interaksi dinamis --}}


    <div class="modal fade" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Peringatan!!
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Apakah anda yakin ingin menghapus data ini?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>
                    <form action="/surats" method="post" class="d-inline" id="formDelete">
                        @method('delete')
                        @csrf
                        <button class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Accept</span>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>



    {{-- ================================================== --}}
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Tambah Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Ganti action dan method sesuai route Anda --}}
                {{-- <form action="{{ route('nama.route.simpan') }}" method="POST"> --}}
                <form id="formInsert" action="/surats" method="POST"> {{-- Action dikosongkan sementara --}}
                    @csrf {{-- Jangan lupa CSRF token untuk form POST --}}
                    <div class="modal-body">
                        {{-- Isi form tambah nomor di sini --}}
                        <div class="mb-3">
                            <label for="nomor_surat" class="form-label">Nomor Surat</label>
                            <input type="text" class="form-control" id="nomor_surat" name="nomor_surat"
                                placeholder="Nomor surat" required>
                            <input type="hidden" id="nomor_rekening" name="nomor_rekening"
                                value="{{ $nasabah->NO_REK ?? '-' }}">
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_surat" class="form-label">tanggal Surat</label>
                            <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat"
                                placeholder="tanggal surat" required>
                        </div>


                        <div class="mb-3">
                            <label for="jenis_surat" class="form-label">Jenis Surat</label>
                            <select class="form-select" id="jenis_surat" name="jenis_surat" required>
                                <option selected disabled value="">Pilih Jenis Surat...</option>
                                @if ($cek == 0)
                                    <option value="0">Surat Teguran</option>
                                @elseif ($cek == 1)
                                    <option value="1">Surat Peringatan 1</option>
                                @elseif ($cek == 2)
                                    <option value="2">Surat Peringatan 2</option>
                                @elseif ($cek == 3)
                                    <option value="3">Surat Peringatan 3</option>
                                    @else

                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat"
                                placeholder="Alamat" required>
                        </div>


                        <div class="mb-3">
                            <label for="tunggakan_pokok" class="form-label">Tunggakan Pokok</label>
                            <input type="float" class="form-control" id="tunggakan_pokok" name="tunggakan_pokok"
                                placeholder="Tunggakan pokok" value="{{ $nasabah->TUNGG_POKOK ?? 0 }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="tunggakan_bunga" class="form-label">Tunggakan Bunga</label>
                            <input type="float" class="form-control" id="tunggakan_bunga" name="tunggakan_bunga"
                                placeholder="Tunggakan bunga" value="{{ $nasabah->TUNGG_BUNGA ?? 0 }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="denda_pokok" class="form-label">Denda Pokok</label>
                            <input type="float" class="form-control" id="denda_pokok" name="denda_pokok"
                                placeholder="Denda pokok" value="{{ $nasabah->DENDA_TUNGGPKK ?? 0 }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="denda_bunga" class="form-label">Denda Bunga</label>
                            <input type="float" class="form-control" id="denda_bunga" name="denda_bunga"
                                placeholder="Denda bunga" value="{{ $nasabah->DENDA_TUNGGBNG ?? 0 }}" required>
                        </div>

                        {{-- Tambahkan field lain sesuai kebutuhan --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-gradient-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================================================== --}}
    {{-- ==          AKHIR STRUKTUR HTML MODAL           == --}}
    {{-- ================================================== --}}


    <script>
        $(function() {
            $(document).on('click', '#delete', function() {
                console.log("HELLO" + $(this).data('id-surat'));
                $('#formDelete').attr('action', '/surats/' + $(this).data('id-surat'))
            })
        });
    </script>
@endsection
