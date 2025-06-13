@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page_title', 'Pengaturan Sistem')

@section('content')
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-user-circle fa-2x mb-2 text-primary"></i>
                    <h5 class="card-title">Profil Akun</h5>
                    <p class="card-text">Lihat dan perbarui informasi akun Anda secara langsung.</p>
                    <a href="{{ route('pengaturan.profil.index') }}" class="btn btn-add w-100">Kelola Profil</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-users-cog fa-2x mb-2 text-success"></i>
                    <h5 class="card-title">Manajemen Pengguna</h5>
                    <p class="card-text">Atur akses, tambah, dan kelola data pengguna sistem.</p>
                    <a href="{{ route('pengaturan.pengguna') }}" class="btn btn-add w-100">Kelola Pengguna</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-file-excel fa-2x mb-2 text-warning"></i>
                    <h5 class="card-title">Laporan Aset</h5>
                    <p class="card-text">Ekspor seluruh data aset dalam format Excel untuk dokumentasi atau analisis.</p>
                    <a href="{{ route('pengaturan.laporan.aset.excel') }}" class="btn btn-add w-100">Unduh Laporan Aset</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-clipboard-list fa-2x mb-2 text-info"></i>
                    <h5 class="card-title">Laporan Aktivitas Aset</h5>
                    <p class="card-text">Ekspor semua aktivitas terkait aset: penerimaan, pengecekan, dan penghapusan.</p>
                    <a href="{{ route('pengaturan.laporan.aktivitas.excel') }}" class="btn btn-add w-100">Unduh Laporan
                        Aktivitas</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-toggle-on fa-2x mb-2 text-danger"></i>
                    <h5 class="card-title">Aktivasi Aset</h5>
                    <p class="card-text">Kelola aset tidak aktif dan aktifkan kembali sesuai kebutuhan.</p>
                    <a href="{{ route('pengaturan.pengaktifan') }}" class="btn btn-add w-100">Kelola Aktivasi</a>
                </div>
            </div>
        </div>
    </div>
@endsection
