{{-- filepath: /Users/razitulikhlas/whatsaAppBlast/resources/views/nominatif.blade.php --}}
@extends('layouts.main')
@section('content')
 <div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Upload data nominatif</h4>
              @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
              <form class="forms-sample" action="{{ route('nominatif.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <div class="mb-3">
                        <label>File upload</label>
                        <input type="file" name="file" class="form-control file-upload-info"  placeholder="Upload CSV File">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_nominatif" class="form-label">Tanggal Nominatif</label>
                        <input type="date" class="form-control" id="tanggal_nominatif" name="tanggal_nominatif"
                            placeholder="tanggal surat" required>
                    </div>

                    <span class="input-group-append">
                        <button type="submit" class="btn btn-primary mr-2">Upload</button>
                    </span>
                </div>

              </form>
            </div>
          </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">DPK</h4>
                <div class="table-responsive">
                  <table id="nasabahTableDPK"  class="table">
                    <thead>
                      <tr>
                        <th>NO</th>
                        <th>Tanggal</th>
                        <th>Lancar</th>
                        <th>DPK</th>
                        <th>NPL</th>
                      </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item ) {{-- Menggunakan @forelse --}}
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->TANGGAL }}</td>
                            <td>{{ number_format((float)($item->Lancar ?? 0), 0, ',', '.') }}</td>
                            <td>{{ number_format((float)($item->DPK ?? 0), 0, ',', '.') }}</td>
                            <td>{{ number_format((float)($item->NPL ?? 0), 0, ',', '.')}}</td>
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



@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/file-upload.js') }}"></script>
@endsection
