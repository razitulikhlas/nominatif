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
                        <th>Action</th>
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
                            <td> <button type="button" class="btn btn-danger rounded-pill"
                                data-bs-toggle="modal" data-nominatif="{{$item->TANGGAL}}"
                                data-bs-target="#exampleModalCenter" id="delete">Delete</button>
                            </td>
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
            <form action="/nominatif" method="post" class="d-inline" id="formDelete">
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

<script>
    $(function() {
        $(document).on('click', '#delete', function() {
            console.log("HELLO" + $(this).data('nominatif'));
            $('#formDelete').attr('action', '/nominatif/' + $(this).data('nominatif'));
        })
    });
</script>


@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/js/file-upload.js') }}"></script>



@endsection
