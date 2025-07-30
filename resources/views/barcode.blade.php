@extends('layouts.main')
@section('content')
<div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">
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

    {{-- Asumsi variabel ini ada dari controller Anda --}}
    @php
        // Hapus atau ganti ini dengan data aktual dari controller Anda
        // Ini hanya untuk contoh agar kode di bawah bisa berjalan
        $isWhatsAppConnected = false; // Ganti dengan true jika sudah terhubung
        $whatsAppProfilePicUrl = 'https://via.placeholder.com/100'; // URL gambar profil
        $whatsAppQrCodeUrl = 'https://via.placeholder.com/200'; // URL QR Code
        $whatsAppAccountName = 'Nama Pengguna WhatsApp';
        $whatsAppPhoneNumber = '+6281234567890';
    @endphp

    <div class="row">
        {{-- Kolom Biru - Status Koneksi & Scan --}}
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body text-center" style="background-color: #007bff; color: white; border-radius: .25rem; display: flex; flex-direction: column; justify-content: center; align-items: center;">
              @if ($isWhatsAppConnected)
                <img src="{{ $whatsAppProfilePicUrl }}" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="width: 100px; height: 100px; border: 3px solid white;">
                <h4 class="card-title mb-2" style="color: white;">Terhubung!</h4>
                <p><i class="mdi mdi-whatsapp"></i> WhatsApp Terkoneksi</p>
              @else
                <h4 class="card-title mb-3" style="color: white;">Scan Barcode WhatsApp</h4>
                <p class="mb-2">Arahkan kamera WhatsApp Anda ke kode QR di bawah ini untuk menghubungkan akun.</p>
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/43/WhatsApp_click-to-chat_QR_code.png" alt="WhatsApp QR Code" class="img-fluid mb-3" style="max-width: 200px; border: 5px solid white; border-radius: .25rem;">
                {{-- Anda bisa menambahkan tombol refresh QR jika QR code dinamis --}}
                {{-- <button class="btn btn-light btn-sm">Refresh QR Code</button> --}}
              @endif
            </div>
          </div>
        </div>

        {{-- Kolom Merah - Informasi Akun --}}
        <div class="col-md-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body" style="background-color: #dc3545; color: white; border-radius: .25rem; display: flex; flex-direction: column; justify-content: center;">
              <h4 class="card-title mb-3 text-center" style="color: white;">Informasi Akun</h4>
              @if ($isWhatsAppConnected)
                <div class="mb-2">
                  <strong style="display: block; margin-bottom: .25rem;"><i class="mdi mdi-account"></i> Nama Akun:</strong>
                  <span>{{ $whatsAppAccountName }}</span>
                </div>
                <div>
                  <strong style="display: block; margin-bottom: .25rem;"><i class="mdi mdi-phone"></i> Nomor HP:</strong>
                  <span>{{ $whatsAppPhoneNumber }}</span>
                </div>
              @else
                <div class="mb-2">
                  <strong style="display: block; margin-bottom: .25rem;"><i class="mdi mdi-account-off"></i> Nama Akun:</strong>
                  <span>Belum Terhubung</span>
                </div>
                <div>
                  <strong style="display: block; margin-bottom: .25rem;"><i class="mdi mdi-phone-off"></i> Nomor HP:</strong>
                  <span>Belum Terhubung</span>
                </div>
                <p class="mt-3 text-center">
                  <small>Informasi akun akan tampil di sini setelah Anda berhasil melakukan scan barcode.</small>
                </p>
              @endif
            </div>
          </div>
        </div>
      </div>


  </div> {{-- Akhir content-wrapper --}}

  {{-- Modal Tambah Nomor (jika masih diperlukan) --}}
  {{-- <div class="modal fade" id="myModal" ...> ... </div> --}}
  {{-- Modal Edit Nomor (jika masih diperlukan) --}}
  {{-- <div class="modal fade" id="editModal" ...> ... </div> --}}

@endsection

@push('styles')
{{-- Tambahkan CSS kustom di sini jika perlu, misalnya untuk styling yang lebih spesifik --}}
<style>
    .grid-margin.stretch-card .card,
    .grid-margin.stretch-card .card .card-body {
        height: 100%; /* Memastikan card dan card-body mengisi tinggi kolom */
    }
</style>
@endpush

{{-- Pastikan jQuery dan skrip modal Anda dimuat jika modal masih digunakan --}}
{{-- @push('scripts')
<script>
// Skrip untuk modal tambah dan edit jika masih ada
$(function() {
    // ... skrip modal Anda ...
});
</script>
@endpush --}}
