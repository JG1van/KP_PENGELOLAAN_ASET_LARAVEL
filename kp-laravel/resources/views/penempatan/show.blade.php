@extends('layouts.app')

@section('title', 'Detail Penempatan Aset')
@section('page_title', 'Detail Penempatan Aset')

@section('content')
    <div class="alas p-3">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="idPenempatan" class="form-label">ID Penempatan</label>
                <input type="text" class="form-control" value="{{ $penempatan->Id_Penempatan }}" readonly>
            </div>
            <div class="col-md-6">
                <label for="tanggalPenempatan" class="form-label">Tanggal</label>
                <input type="date" class="form-control" value="{{ $penempatan->Tanggal_Penempatan }}" readonly>
            </div>
        </div>

        <a href="{{ route('penempatan.export-excel', $penempatan->Id_Penempatan) }}" class="btn btn-add w-100">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>

    </div>

    @php
        $grouped = $penempatan->detail->groupBy('Id_Lokasi');
    @endphp

    @foreach ($lokasi as $lok)
        <h5 class="mt-4">Lokasi: {{ $lok->Nama_Lokasi }}</h5>
        <div class="table-responsive">
            <table class="table table-bordered w-100 table-hover text-center align-middle">
                <thead class="align-middle">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">ID Aset</th>
                        <th style="width: 45%;">Nama Aset</th>
                        <th style="width: 25%;">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grouped[$lok->Id_Lokasi] ?? [] as $i => $detail)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $detail->aset->Id_Aset }}</td>
                            <td>{{ $detail->aset->Nama_Aset }}</td>
                            <td>{{ $detail->aset->kategori->Nama_Kategori ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">Tidak ada aset pada lokasi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach
@endsection
