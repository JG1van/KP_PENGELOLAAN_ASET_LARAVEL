@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Pengguna')

@section('content')
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-user-plus"></i> Tambah Pengguna
        </button>
    </div>

    <div class="alas p-3">
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle equal-width-table">
                <thead class="align-middle">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $i => $user)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->status === 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah Pengguna --}}
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <form id="formTambahUser" action="{{ route('pengaturan.pengguna.store') }}" method="POST" class="modal-content"
                autocomplete="off">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required placeholder="Masukkan Nama"
                            autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="Masukkan Email"
                            autocomplete="off" autocapitalize="off" autocorrect="off">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Masukkan Password"
                            autocomplete="new-password">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100" id="btnSubmitUser">
                        <span id="btnSubmitUserText">Simpan</span>
                        <span id="btnSubmitUserLoading" class="d-none">
                            <i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formTambahUser');
            const submitBtn = document.getElementById('btnSubmitUser');
            const submitBtnText = document.getElementById('btnSubmitUserText');
            const submitBtnLoading = document.getElementById('btnSubmitUserLoading');

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtnText.classList.add('d-none');
                submitBtnLoading.classList.remove('d-none');
            });

            @if ($errors->any())
                var modal = new bootstrap.Modal(document.getElementById('modalTambah'));
                modal.show();
            @endif
        });
    </script>
@endsection
