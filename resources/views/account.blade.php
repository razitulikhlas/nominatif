@extends('layouts.main')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">
        {{-- Mengganti ikon agar lebih relevan --}}
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-barcode-scan"></i>
        </span> Scan Barcode / Nomor WhatsApp
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item active" aria-current="page">
            <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
          </li>
        </ul>
      </nav>
    </div>

    {{-- Contoh Alert (opsional, bisa dihapus) --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
    @if(session('error'))
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
                      <h4 class="card-title mb-0">Daftar Nomor WhatsApp</h4> {{-- Judul lebih deskriptif --}}
                  </div>
                  <div class="col-md-2 text-end">
                      {{-- Menambahkan data-bs-toggle="modal" --}}
                      <button type="button" class="btn btn-gradient-info btn-rounded btn-fw" data-bs-toggle="modal" data-bs-target="#myModal">
                        <i class="mdi mdi-plus"></i> Tambah
                      </button>
                  </div>
              </div>

              {{-- Menambahkan div table-responsive untuk tabel --}}
              <div class="table-responsive">
                <table class="table table-hover"> {{-- Menambahkan class table-hover (opsional) --}}
                  <thead>
                    <tr>
                      <th>Nomor</th>
                      <th>phone</th>
                      <th>Dibaca</th>
                      <th>Tolak Panggilan</th>
                      <th>Status Pengetikan</th>
                      <th>Tersedia</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($data as $item ) {{-- Menggunakan @forelse --}}
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->phone }}</td>
                        <td>
                            @if ($item->status_read == 1)
                                <label class="badge badge-gradient-success">Ya</label>
                            @else
                                <label class="badge badge-gradient-danger">Tidak</label>
                            @endif
                        </td>
                        <td>
                            @if ($item->status_call == 1)
                                <label class="badge badge-gradient-success">Ya</label>
                            @else
                                <label class="badge badge-gradient-danger">Tidak</label>
                            @endif
                        </td>
                        <td>
                            @if ($item->status_type == 1)
                                <label class="badge badge-gradient-success">Ya</label>
                            @else
                                <label class="badge badge-gradient-danger">Tidak</label>
                            @endif
                        </td>
                        <td>
                            @if ($item->status_available == 1)
                                <label class="badge badge-gradient-success">Ya</label>
                            @else
                                <label class="badge badge-gradient-danger">Tidak</label>
                            @endif
                        </td>
                        <td>
                            {{-- Tombol Edit --}}
                            <button type="button" class="btn btn-gradient-warning btn-sm p-1 edit-button" id="edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="{{ $item->id }}"
                                    data-phone="{{ $item->phone }}"
                                    data-status_read="{{ $item->status_read }}"
                                    data-status_call="{{ $item->status_call }}"
                                    data-status_type="{{ $item->status_type }}"
                                    data-status_available="{{ $item->status_available }}"
                                    title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </button>

                            {{-- Form untuk Tombol Delete --}}
                            <form action="{{ route('number.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus nomor ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-gradient-danger btn-sm p-1" title="Hapus">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </form>

                            {{-- Tombol Scan Barcode (Contoh, sesuaikan fungsionalitasnya) --}}
                            <a href="/barcode" class="btn btn-gradient-info btn-sm p-1" title="Lihat Barcode untuk {{ $item->phone }}">
                                <i class="mdi mdi-barcode-scan"></i>
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

</div> {{-- Akhir content-wrapper --}}


  {{-- ================================================== --}}
{{-- ==            STRUKTUR HTML MODAL EDIT          == --}}
{{-- ================================================== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Nomor WhatsApp</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formEdit" action="" method="POST"> {{-- Action akan diatur oleh JavaScript --}}
        @method('put')
           @csrf{{-- Atau PATCH, sesuaikan dengan metode controller Anda --}}
            <div class="modal-body">
                <input type="hidden" id="edit_id" name="id"> {{-- Untuk menyimpan ID item yang diedit --}}

                <div class="mb-3">
                  <label for="phone" class="form-label">Nomor WhatsApp</label>
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="+628xxxxxxxxxx" required>
                </div>

                <div class="form-switch mb-3">
                    <input type="hidden" name="status_read" value="0"> {{-- Default value jika tidak diceklis --}}
                    <input class="form-check-input" type="checkbox" role="switch" id="edit_status_read" name="status_read" value="1">
                    <label class="form-check-label" for="edit_status_read">Aktifkan Status Dibaca</label>
                </div>

                <div class="form-switch mb-3">
                  <input type="hidden" name="status_call" value="0">
                  <input class="form-check-input" type="checkbox" role="switch" id="edit_status_call" name="status_call" value="1">
                  <label class="form-check-label" for="edit_status_call">Tolak Panggilan Masuk</label>
                </div>

                <div class="form-switch mb-3">
                    <input type="hidden" name="status_type" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="edit_status_type" name="status_type" value="1">
                    <label class="form-check-label" for="edit_status_type">Aktifkan Status Pengetikan</label>
                </div>

                <div class="form-switch mb-3">
                    <input type="hidden" name="status_available" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="edit_status_available" name="status_available" value="1">
                    <label class="form-check-label" for="edit_status_available">Aktifkan status tersedia</label>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="submit" class="btn btn-gradient-primary">Simpan Perubahan</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  {{-- ================================================== --}}
  {{-- ==          AKHIR STRUKTUR HTML MODAL EDIT      == --}}
  {{-- ================================================== --}}


  {{-- ================================================== --}}
  {{-- ==            STRUKTUR HTML MODAL               == --}}
  {{-- ================================================== --}}
  <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="myModalLabel">Tambah Nomor WhatsApp Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        {{-- Ganti action dan method sesuai route Anda --}}
        {{-- <form action="{{ route('nama.route.simpan') }}" method="POST"> --}}
        <form id="formInsert" action="/number" method="POST"> {{-- Action dikosongkan sementara --}}
            @csrf {{-- Jangan lupa CSRF token untuk form POST --}}
            <div class="modal-body">
                {{-- Isi form tambah nomor di sini --}}
                <div class="mb-3">
                  <label for="whatsappNumber" class="form-label">Nomor WhatsApp</label>
                  <input type="text" class="form-control" id="phone" name="phone" placeholder="+628xxxxxxxxxx" required>
                  {{-- Tambahkan validasi error jika perlu --}}
                  {{-- @error('nomor_whatsapp') <div class="text-danger mt-1">{{ $message }}</div> @enderror --}}
                </div>


                {{-- Contoh field lain (misal: toggle switch) --}}
                {{-- Hapus kelas mr-5 dari sini --}}

                <div class="form-switch mb-3">
                    <input type="hidden" name="status_read" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="status_read" name="status_read" value="1" >
                    <label class="form-check-label" for="statusread">Aktifkan Status Dibaca</label>
                </div>


                <div class="form-switch mb-3">
                  <input type="hidden" name="status_call" value="0">
                  <input class="form-check-input" type="checkbox" role="switch" id="status_call" name="status_call" value="1">
                  <label class="form-check-label" for="status_call">Tolak Panggilan Masuk</label>
                </div>

                <div class="form-switch mb-3">
                    <input type="hidden" name="status_type" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="status_type" name="status_type" value="1">
                    <label class="form-check-label" for="status_type">Aktifkan Status Pengetikan</label>
                </div>

                <div class="form-switch mb-3">
                    <input type="hidden" name="status_available" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="status_available" name="status_available" value="1" >
                    <label class="form-check-label" for="status_available">Aktifkan status tersedia</label>
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
        $(document).on('click','#edit',function(){
            console.log("sukses");

            // Ekstrak info dari atribut data-* menggunakan jQuery .data()
            var id = $(this).data('id');
            var phone = $(this).data('phone');
            var statusRead = $(this).data('status_read');
            var statusCall = $(this).data('status_call');
            var statusType = $(this).data('status_type');
            var statusAvailable = $(this).data('status_available');

            console.log(phone);

            $("#id").val(id);
            $("#phone").val(phone);

            // // Set status checkbox menggunakan jQuery .prop()
            // // Melakukan perbandingan yang lebih aman untuk nilai boolean/string 'true'/'1'
            $('#edit_status_read').prop('checked', (statusRead == 1 || statusRead === true || statusRead === 'true'));
            $('#edit_status_call').prop('checked', (statusCall == 1 || statusCall === true || statusCall === 'true'));
            $('#edit_status_type').prop('checked', (statusType == 1 || statusType === true || statusType === 'true'));
            $('#edit_status_available').prop('checked', (statusAvailable == 1 || statusAvailable === true || statusAvailable === 'true'));
            $('#formEdit').attr('action','/number/'+$(this).data('id'));
        })
    });
    </script>
@endsection
