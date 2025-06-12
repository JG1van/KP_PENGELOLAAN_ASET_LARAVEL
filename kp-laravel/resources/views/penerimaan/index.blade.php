@extends('layouts.app')

@section('title', 'Penerimaan Aset')
@section('page_title', 'Aktivitas Penerimaan Aset')
@section('content')


    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <label class="form-label">Pencarian</label>
            <input id="searchInput" type="text" class="form-control" placeholder="Cari Nama Petugas..." />
        </div>
        <div class="col-md-4">
            <label class="form-label">Tanggal</label>
            <input id="tanggalFilter" type="date" class="form-control" />
        </div>
        <div class="col-md-4">
            <label class="form-label">Urutkan</label>
            <select id="sortSelect" class="form-select">
                <option value="desc">Tanggal Terbaru - Terlama</option>
                <option value="asc">Tanggal Terlama - Terbaru</option>
            </select>
        </div>
    </div>


    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Penerimaan</th>
                    <th>Tanggal Penerimaan</th>
                    <th style="width: 40%">Keterangan</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->Id_Penerimaan }}</td>
                        <td>{{ $row->Tanggal_Terima }}</td>
                        <td class="text-start">{{ $row->Keterangan }}</td>
                        <td>{{ $row->user->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('penerimaan.show', $row->Id_Penerimaan) }}" class="btn btn-sm-1">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
@section('js')
    <script>
        const searchInput = document.getElementById("searchInput");
        const tanggalFilter = document.getElementById("tanggalFilter");
        const sortSelect = document.getElementById("sortSelect");
        const table = document.querySelector("table tbody");

        function updateNomorUrut() {
            const visibleRows = Array.from(table.querySelectorAll("tr")).filter(row => row.style.display !== "none");
            visibleRows.forEach((row, index) => {
                row.children[0].innerText = index + 1;
            });
        }

        function filterTable() {
            const search = searchInput.value.toLowerCase();
            const tanggal = tanggalFilter.value;
            const rows = table.querySelectorAll("tr");

            rows.forEach(row => {
                const petugas = row.children[4]?.innerText.toLowerCase();
                const tanggalData = row.children[2]?.innerText;

                const cocokSearch = !search || petugas.includes(search);
                const cocokTanggal = !tanggal || tanggalData === tanggal;

                row.style.display = (cocokSearch && cocokTanggal) ? "" : "none";
            });

            updateNomorUrut();
        }

        function sortTable() {
            const rows = Array.from(table.querySelectorAll("tr")).sort((a, b) => {
                const dateA = new Date(a.children[2].innerText);
                const dateB = new Date(b.children[2].innerText);

                return sortSelect.value === "asc" ? dateA - dateB : dateB - dateA;
            });

            rows.forEach(row => table.appendChild(row));
        }

        searchInput.addEventListener("keyup", filterTable);
        tanggalFilter.addEventListener("change", filterTable);
        sortSelect.addEventListener("change", () => {
            sortTable();
            filterTable();
        });

        // Inisialisasi urutan saat halaman dimuat
        updateNomorUrut();
    </script>
@endsection
