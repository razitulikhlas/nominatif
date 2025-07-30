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
              <form class="forms-sample" action="{{ route('afiliasi.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <div class="mb-3">
                        <label>File upload</label>
                        <input type="file" name="file" class="form-control file-upload-info"  placeholder="Upload CSV File">
                    </div>

                    <span class="input-group-append">
                        <button type="submit" class="btn btn-primary mr-2">Upload</button>
                    </span>
                </div>

              </form>
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
