@extends('layouts.app')

@section('title', 'Detail Pengecekan Aset')
@section('page_title', 'Detail Pengecekan Aset')

@section('content')
    <div class="alas p-3">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="idPengecekan" class="form-label">ID Pengecekan</label>
                <input type="text" class="form-control" id="idPengecekan" value="{{ $pengecekan->Id_Pengecekan }}" readonly>
            </div>
            <div class="col-md-6">
                <label for="tanggalPengecekan" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggalPengecekan"
                    value="{{ $pengecekan->Tanggal_Pengecekan }}" readonly>
            </div>
        </div>
        <a href="{{ route('pengecekan.export-excel', $pengecekan->Id_Pengecekan) }}" class="btn btn-add w-100">
            Export Excel
        </a>

    </div>

    @php
        $grouped = $pengecekan->detail->groupBy('Kondisi');
    @endphp

    @foreach (['Baik', 'Rusak Sedang', 'Rusak Berat', 'Hilang', 'Diremajakan'] as $kondisi)
        <h5 class="mt-4">Daftar Aset - {{ $kondisi }}</h5>
        <div class="table-responsive">
            <table class="table table-bordered w-100 table-hover text-center align-middle">
                <thead class="align-middle">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">ID Aset</th>
                        <th style="width: 40%;">Nama Aset</th>
                        <th style="width: 30%;">Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grouped[$kondisi] ?? [] as $i => $detail)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $detail->aset->Id_Aset }}</td>
                            <td>{{ $detail->aset->Nama_Aset }}</td>
                            <td>{{ $detail->aset->kategori->Nama_Kategori ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">Tidak ada aset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach



@endsection
