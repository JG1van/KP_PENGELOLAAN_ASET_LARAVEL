@extends('layouts.app')

@section('title', 'Aktivitas Penghapusan Aset')
@section('page_title', 'Aktivitas Penghapusan Aset')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">

    </div>

    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <label class="form-label">Pencarian</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari...">
        </div>
        <div class="col-md-4">
            <label class="form-label">Tanggal</label>
            <input type="date" id="filterTanggal" class="form-control">
        </div>

        <div class="col-md-4 text-end">
            <label class="form-label d-block invisible">.</label>
            <a href="{{ route('penghapusan.create') }}" class="btn btn-add w-100">
                <i class="fas fa-plus me-2"></i> Tambah Aktivitas Penghapusan
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered w-100 table-hover text-center align-middle" id="tabelPenghapusan">
            <thead class="table-container">
                <tr>
                    <th>No</th>
                    <th>ID Penghapusan</th>
                    <th>Tanggal Penghapusan</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data->sortBy('Id_Penghapusan') as $index => $hapus)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $hapus->Id_Penghapusan }}</td>
                        <td>{{ $hapus->Tanggal_Hapus }}</td>
                        <td>{{ $hapus->user->name ?? '-' }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('penghapusan.show', $hapus->Id_Penghapusan) }}"
                                    class="btn btn-sm-1">Detail</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted">Tidak ada data penghapusan aset.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("searchInput");
            const filterTanggal = document.getElementById("filterTanggal");

            function filterTable() {
                const keyword = searchInput.value.toLowerCase();
                const tanggal = filterTanggal.value;
                const rows = document.querySelectorAll("#tabelPenghapusan tbody tr");

                rows.forEach(row => {
                    const teks = row.innerText.toLowerCase();
                    const tanggalRow = row.children[2].innerText.trim();
                    const cocokTeks = teks.includes(keyword);
                    const cocokTanggal = !tanggal || tanggalRow === tanggal;

                    row.style.display = cocokTeks && cocokTanggal ? "" : "none";
                });
            }

            searchInput.addEventListener("input", filterTable);
            filterTanggal.addEventListener("change", filterTable);
        });
    </script>
@endsection
