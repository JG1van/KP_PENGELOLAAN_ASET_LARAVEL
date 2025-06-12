@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page_title', 'Halaman Pengaturan')

@section('content')
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-user fa-2x mb-2"></i>
                    <h5 class="card-title">Profil Akun</h5>
                    <p class="card-text">Kelola informasi akun</p>
                    <a href="{{ route('pengaturan.profil.index') }}" class="btn btn-add w-100">Kelola Profil</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-users-cog fa-2x mb-2"></i>
                    <h5 class="card-title">Manajemen Pengguna</h5>
                    <p class="card-text">Tambah atau ubah data pengguna</p>
                    <a href="{{ route('pengaturan.pengguna') }}" class="btn btn-add w-100">Kelola Pengguna</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-file-export fa-2x mb-2"></i>
                    <h5 class="card-title">Laporan Aset</h5>
                    <p class="card-text">Ekspor data aset secara lengkap</p>
                    <a href="{{ route('pengaturan.laporan.aset.excel') }}" class="btn btn-add w-100">Lihat Laporan Aset</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-tasks fa-2x mb-2"></i>
                    <h5 class="card-title">Laporan Aktivitas</h5>
                    <p class="card-text">Ekspor aktivitas penerimaan, pengecekan, & penghapusan</p>
                    <a href="{{ route('pengaturan.laporan.aktivitas.excel') }}" class="btn btn-add w-100">Lihat Laporan
                        Aktivitas</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center h-200">
                <div class="card-body">
                    <i class="fas fa-power-off fa-2x mb-2"></i>
                    <h5 class="card-title">Pengaktifan Aset</h5>
                    <p class="card-text">Kelola dan aktifkan kembali aset yang tidak aktif</p>
                    <a href="{{ route('pengaturan.pengaktifan') }}" class="btn btn-add w-100">Kelola Pengaktifan</a>
                </div>
            </div>
        </div>

    </div>
@endsection
