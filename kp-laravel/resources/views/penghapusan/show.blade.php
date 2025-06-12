@extends('layouts.app')

@section('title', 'Detail Penghapusan Aset')
@section('page_title', 'Detail Penghapusan Aset')

@section('content')
    <div class="alas p-3">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="idPenghapusan" class="form-label">ID Penghapusan</label>
                <input type="text" class="form-control" id="idPenghapusan" value="{{ $penghapusan->Id_Penghapusan }}"
                    readonly>
            </div>
            <div class="col-md-6">
                <label for="tanggalHapus" class="form-label">Tanggal Penghapusan</label>
                <input type="date" class="form-control" id="tanggalHapus" value="{{ $penghapusan->Tanggal_Hapus }}"
                    readonly>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Dokumen Penghapusan</label><br>
            {{-- <a href="{{ asset('storage/dokumen_penghapusan/' . $penghapusan->Dokumen_Penghapusan) }}" target="_blank"
                class="btn btn-sm-2 w-100">
                <i class="fas fa-file-alt me-2"></i>Lihat Dokumen
            </a> --}}
        </div>
        @foreach ($files as $file)
            <div class="mt-3">
                <button onclick="window.open('{{ $file['secure_url'] }}', '_blank')" class="btn btn-sm-2 w-100">
                    Lihat Dokumen: {{ basename($file['public_id']) }}
                </button>
            </div>
        @endforeach
    </div>

    <h5 class="mt-4">Daftar Aset yang Dihapus</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">ID Aset</th>
                    <th style="width: 40%;">Nama Aset</th>
                    <th style="width: 30%;">Kategori</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penghapusan->detail as $i => $detail)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $detail->aset->Id_Aset }}</td>
                        <td>{{ $detail->aset->Nama_Aset }}</td>
                        <td>{{ $detail->aset->kategori->Nama_Kategori ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">Tidak ada aset yang dihapus.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
