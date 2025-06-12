@extends('layouts.app')

@section('title', 'Profil Akun')
@section('page_title', 'Profil Akun')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alas p-3">
        <form id="updateProfileForm" action="{{ route('pengaturan.profil.updateAll') }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            <h5>Informasi Akun</h5>
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Password Lama</label>
                <input type="password" name="current_password" class="form-control" autocomplete="new-password">
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" autocomplete="new-password">
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password">
            </div>

            <button type="button" class="btn btn-add w-100" id="btnConfirmSave">
                <i class="fas fa-save me-2"></i> Simpan Semua Perubahan
            </button>
        </form>
    </div>

    <form id="deactivateForm" action="{{ route('pengaturan.profil.nonaktif') }}" method="POST">
        @csrf
        @method('DELETE')
    </form>

    <button type="button" class="btn btn-danger w-100 mt-3" id="btnConfirmDeactivate">
        <i class="fas fa-user-slash me-2"></i> Nonaktifkan Akun
    </button>

@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('globalConfirmModal');
            const modal = new bootstrap.Modal(modalElement);
            const confirmMessage = document.getElementById('globalConfirmMessage');
            const confirmOkBtn = document.getElementById('globalConfirmOk');

            let confirmAction = null;

            function showConfirmModal(message, actionCallback) {
                confirmMessage.textContent = message;
                confirmOkBtn.disabled = false;
                confirmOkBtn.innerHTML = 'OK'; // Reset tombol
                confirmAction = actionCallback;
                modal.show();
            }

            const saveBtn = document.getElementById('btnConfirmSave');
            if (saveBtn) {
                saveBtn.addEventListener('click', function() {
                    showConfirmModal('Yakin ingin menyimpan semua perubahan?', function() {
                        // Tambahkan spinner
                        confirmOkBtn.disabled = true;
                        confirmOkBtn.innerHTML =
                            '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
                        document.getElementById('updateProfileForm').submit();
                    });
                });
            }

            const deactivateBtn = document.getElementById('btnConfirmDeactivate');
            if (deactivateBtn) {
                deactivateBtn.addEventListener('click', function() {
                    showConfirmModal('Yakin ingin menonaktifkan akun ini?', function() {
                        confirmOkBtn.disabled = true;
                        confirmOkBtn.innerHTML =
                            '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
                        document.getElementById('deactivateForm').submit();
                    });
                });
            }

            // Jangan tutup modal sampai halaman reload (tidak modal.hide di sini)
            confirmOkBtn.addEventListener('click', function() {
                if (typeof confirmAction === 'function') {
                    confirmAction();
                }
            });
        });
    </script>
@endsection
