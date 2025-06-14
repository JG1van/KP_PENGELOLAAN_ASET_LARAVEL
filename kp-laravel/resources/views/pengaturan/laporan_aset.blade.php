@extends('layouts.app')

@section('title', 'Laporan Aset')
@section('page_title', 'Laporan Rekap Data Aset')

@section('content')
    <a href="{{ route('laporan.aset.excel') }}" class="btn btn-add mb-5">
        <i class="fas fa-file-excel me-2"></i> Ekspor ke Excel
    </a>


    <div class="table-responsive">
        <table class="table table-bordered w-100 table-hover text-center align-middle equal-width-table">
            <thead class="align-middle">
                <tr>
                    <th>No</th>
                    <th>ID Aset</th>
                    <th>Nama Aset</th>
                    <th>Kategori</th>
                    <th>Tanggal Penerimaan</th>
                    <th>Kondisi</th>
                    <th>Status</th>
                    <th>Nilai Awal</th>
                    <th>Nilai Sekarang</th>
                    <th>Riwayat Penurunan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asets as $index => $aset)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $aset->Id_Aset }}</td>
                        <td>{{ $aset->Nama_Aset }}</td>
                        <td>{{ $aset->kategori->Nama_Kategori ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($aset->Tanggal_Penerimaan)->format('d-m-Y') }}</td>
                        <td>{{ $aset->Kondisi }}</td>
                        <td>{{ ucfirst($aset->STATUS) }}</td>
                        <td>Rp {{ number_format($aset->Nilai_Aset_Awal, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $latestPenurunan = $aset->penurunans->sortByDesc('Tahun')->first();
                            @endphp
                            Rp {{ number_format($latestPenurunan->Nilai_Saat_Ini ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="text-start">
                            @forelse($aset->penurunans->sortBy('Tahun') as $p)
                                <div>
                                    Tahun {{ $p->Tahun }}: Rp {{ number_format($p->Nilai_Saat_Ini, 0, ',', '.') }}
                                </div>
                            @empty
                                <div class="text-muted">Tidak ada data</div>
                            @endforelse
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-muted">Tidak ada data aset.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
