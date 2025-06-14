@extends('layouts.app')

@section('title', 'Aktivitas Penempatan Aset')
@section('page_title', 'Aktivitas Penempatan Aset')

@section('content')

    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama Petugas...">
        </div>
        <div class="col-md-4">
            <input type="date" id="filterTanggal" class="form-control">
        </div>
        <div class="col-md-4 text-end">
            <label class="form-label d-block invisible">.</label>
            <a href="{{ route('penempatan.create') }}" class="btn btn-add w-100">
                <i class="fas fa-plus me-2"></i> Tambah Penempatan
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered w-100 table-hover text-center align-middle">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 20%;">Tanggal</th>
                    <th style="width: 30%;">Petugas</th>
                    <th style="width: 15%;">Jumlah Aset</th>
                    <th style="width: 20%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->Id_Penempatan }}</td>
                        <td>{{ $item->Tanggal_Penempatan }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->detail->count() }}</td>
                        <td>
                            <a href="{{ route('penempatan.show', $item->Id_Penempatan) }}" class="btn btn-sm-1">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Belum ada data penempatan.</td>
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
                    const namaPetugas = row.cells[3]?.textContent?.toLowerCase() || '';
                    const tanggalPenempatan = row.cells[2]?.textContent?.trim() || '';

                    const cocokNama = namaPetugas.includes(keyword);
                    const cocokTanggal = !tanggal || tanggalPenempatan === tanggal;

                    row.style.display = (cocokNama && cocokTanggal) ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterTable);
            filterTanggal.addEventListener('change', filterTable);
        });
    </script>
@endsection
