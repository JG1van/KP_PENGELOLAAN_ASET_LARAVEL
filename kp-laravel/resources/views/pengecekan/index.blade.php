@extends('layouts.app')

@section('title', 'Pengecekan Aset')
@section('page_title', 'Aktivitas Pengecekan Aset')

@section('content')


    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <label class="form-label">Pencarian</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama Petugas...">
        </div>
        <div class="col-md-4">
            <label class="form-label">Tanggal</label>
            <input type="date" id="filterTanggal" class="form-control">
        </div>
        <div class="col-md-4 text-end">
            <label class="form-label d-block invisible">.</label>
            <a href="{{ route('pengecekan.create') }}" class="btn btn-add w-100">
                <i class="fas fa-plus me-2"></i> Buat Aktivitas Baru
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle equal-width-table">
            <thead class="align-middle">
                <tr>
                    <th>No</th>
                    <th>ID Pengecekan</th>
                    <th>Tanggal</th>
                    <th>Petugas</th>
                    <th>Jumlah Barang</th>
                    <th>Baik</th>
                    <th>Rusak<br>Sedang</th>
                    <th>Rusak<br>Berat</th>
                    <th>Hilang</th>
                    <th>Diremajakan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->Id_Pengecekan }}</td>
                        <td>{{ $row->Tanggal_Pengecekan }}</td>
                        <td>{{ $row->user->name ?? '-' }}</td>
                        <td>{{ $row->detail->count() }}</td>
                        <td>{{ $row->detail->where('Kondisi', 'Baik')->count() }}</td>
                        <td>{{ $row->detail->where('Kondisi', 'Rusak Sedang')->count() }}</td>
                        <td>{{ $row->detail->where('Kondisi', 'Rusak Berat')->count() }}</td>
                        <td>{{ $row->detail->where('Kondisi', 'Hilang')->count() }}</td>
                        <td>{{ $row->detail->where('Kondisi', 'Diremajakan')->count() }}</td>
                        <td class="text-center">
                            <a href="{{ route('pengecekan.show', $row->Id_Pengecekan) }}" class="btn btn-sm-1">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-muted">Tidak ada data pengecekan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterTanggal = document.getElementById('filterTanggal');

            function filterTable() {
                const keyword = searchInput.value.toLowerCase();
                const tanggal = filterTanggal.value;
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const namaPetugas = row.cells[3].textContent.toLowerCase();
                    const tanggalPengecekan = row.cells[2].textContent.trim();

                    const cocokNama = namaPetugas.includes(keyword);
                    const cocokTanggal = !tanggal || tanggalPengecekan === tanggal;

                    if (cocokNama && cocokTanggal) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            filterTanggal.addEventListener('change', filterTable);
        });
    </script>

@endsection
