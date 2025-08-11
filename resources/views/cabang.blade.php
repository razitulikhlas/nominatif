{{-- filepath: /Users/razitulikhlas/whatsaAppBlast/resources/views/nominatif.blade.php --}}
@extends('layouts.main')
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-4 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Cabang</h4>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form class="forms-sample" action="/cabang" method="POST">
                            @csrf
                            <div class="form-group">
                                <div class="mb-3">
                                    <label for="kode_cabang" class="form-label">Kode Cabang</label>
                                    <input type="kode_cabang" class="form-control" id="kode_cabang" name="kode_cabang"
                                        placeholder="Kode Cabang" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nama_cabang" class="form-label">nama Cabang</label>
                                    <input type="text" class="form-control" id="nama_cabang" name="nama_cabang"
                                        placeholder="Nama Cabang" required>
                                </div>

                                <span class="input-group-append">
                                    <button type="submit" class="btn btn-primary mr-2">Tambah</button>
                                </span>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-8 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data user</h4>
                        <div class="table-responsive">
                            <table id="nasabahTableDPK" class="table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Kode Cabang</th>
                                        <th>Nama Cabang</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $item)
                                        {{-- Menggunakan @forelse --}}
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->kode_cabang }}</td>
                                            <td>{{ $item->nama_cabang }}</td>
                                            <td>
                                                <a href=""
                                                class="btn btn-gradient-primary btn-sm p-1 edit-button">
                                                <i class="mdi mdi-eye"></i>
                                            </a>

                                            <button type="button"
                                                class="btn btn-gradient-danger btn-sm p-1 edit-button"
                                                data-bs-toggle="modal" data-id-user={{ $item->id }}
                                                data-bs-target="#exampleModalCenter" id="delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>


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
                    <form action="/cabang" method="post" class="d-inline" id="formDelete">
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
                console.log("HELLO" + $(this).data('id-user'));
                $('#formDelete').attr('action', '/cabang/' + $(this).data('id-user'))
            })
        });
    </script>

@endsection
