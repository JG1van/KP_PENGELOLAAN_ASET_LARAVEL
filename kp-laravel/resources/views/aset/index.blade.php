@extends('layouts.app')

@section('title', 'Daftar Aset')
@section('page_title', 'Daftar Aset')

@section('content')
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <label class="form-label">Pencarian</label>
            <input id="searchInput" type="text" class="form-control" placeholder="Cari Nama Aset..." />
        </div>
        <div class="col-md-2">
            <label class="form-label">Kategori</label>
            <select class="form-select" id="kategoriFilter">
                <option value="">Semua Kategori</option>
                @foreach ($kategoriList as $kategori)
                    <option value="{{ strtolower($kategori->Nama_Kategori) }}">{{ $kategori->Nama_Kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Kondisi</label>
            <select class="form-select" id="kondisiFilter">
                <option value="">Semua Kondisi</option>
                <option>Baik</option>
                <option>Rusak Sedang</option>
                <option>Rusak Berat</option>
                <option>Hilang</option>
            </select>
        </div>
        <div class="col-md-2 text-end">
            <a href="{{ route('aset.create') }}" class="btn btn-add w-100">
                <i class="fas fa-plus me-2"></i>Tambah Aset
            </a>
        </div>
        <div class="col-md-2 text-end">
            <button class="btn btn-add w-100 btn-sm-1" data-bs-toggle="modal" data-bs-target="#penurunanModal">
                <i class="fa-solid fa-angles-down me-2"></i> Penurunan
            </button>
        </div>

    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="align-middle">
                <tr>
                    <th colspan="7" class="text-end align-middle">Batas Penurunan Nilai (%)</th>
                    <th colspan="2">
                        <input id="batasPersenInput" type="number" value="5" min="1" max="100"
                            class="form-control form-control-sm text-center">
                    </th>
                    <th></th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>ID Aset</th>
                    <th style="width: 20%; min-width: 100px">Nama Barang</th>
                    <th>Kategori</th>
                    <th>Tanggal Masuk</th>
                    <th>Status</th>
                    <th>Kondisi</th>
                    <th style="width: 12%;">Nilai Awal</th>
                    <th style="width: 12%;">Nilai Sekarang</th>
                    <th>Aksi</th>
                </tr>

            </thead>
            <tbody>
                @forelse ($data as $index => $aset)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $aset->Id_Aset }}</td>
                        <td>{{ $aset->Nama_Aset }}</td>
                        <td>{{ $aset->kategori->Nama_Kategori ?? '-' }}</td>
                        <td>{{ optional(optional($aset->detailPenerimaan)->penerimaan)->Tanggal_Terima ?? '-' }}</td>
                        <td>{{ $aset->STATUS }}</td>
                        <td>{{ $aset->Kondisi }}</td>
                        <td>Rp {{ number_format($aset->Nilai_Aset_Awal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format(optional($aset->PenurunanTerbaru)->Nilai_Saat_Ini ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            <a href="{{ route('aset.show', $aset->Id_Aset) }}" class="btn btn-sm-1">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-muted">Tidak ada data aset.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" class="text-end">Total Nilai Aset Aktif</th> <!-- 9 karena tambah 1 kolom -->
                    <th colspan="1">Rp {{ number_format($total_nilai_aset_aktif, 0, ',', '.') }}</th>
                    <th></th>
                </tr>
                <tr>
                    <th colspan="8" class="text-end">Total Keseluruhan Nilai Aset</th>
                    <th colspan="1">Rp {{ number_format($total_nilai_aset_keseluruhan, 0, ',', '.') }}</th>
                    <th></th>
                </tr>
            </tfoot>


        </table>
    </div>
    <!-- Modal Penurunan Nilai -->
    <div class="modal fade" id="penurunanModal" tabindex="-1" aria-labelledby="penurunanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('aset.penurunan') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Proses Penurunan Nilai Aset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tahun Penurunan</label>
                            <input type="text" name="tahun" id="tahunPenurunan" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Persentase Penurunan (%)</label>
                            <input type="number" name="persen" value="5" min="1" max="100"
                                class="form-control" required>
                        </div>
                        <div class="alert alert-info small fw-bold">
                            Hanya aset yang belum pernah mengalami penurunan nilai pada tahun ini yang akan diproses.
                            Jika nilai saat ini sudah 0, maka tidak diproses lagi.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnPenurunan" type="submit" class="btn btn-add w-100">
                            <span class="d-inline-block" id="btnPenurunanText">
                                <i class="fas fa-arrow-down me-2"></i> Proses Penurunan
                            </span>
                            <span class="d-none" id="btnPenurunanSpinner">
                                <i class="fas fa-spinner fa-spin me-2"></i> Memproses Penurunan...
                            </span>
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tahunInput = document.getElementById("tahunPenurunan");
            const tahun = new Date().getFullYear();
            tahunInput.value = tahun;
        });
        const searchInput = document.getElementById("searchInput");
        const kategoriFilter = document.getElementById("kategoriFilter");
        const kondisiFilter = document.getElementById("kondisiFilter");
        const batasPersenInput = document.getElementById("batasPersenInput");
        const table = document.querySelector("table tbody");

        function filterTable() {
            const searchValue = searchInput.value.toLowerCase();
            const kategoriValue = kategoriFilter.value.toLowerCase();
            const kondisiValue = kondisiFilter.value.toLowerCase();
            const batasPersen = parseFloat(batasPersenInput.value) || 5;

            const rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName("td");
                if (cells.length === 0) continue;

                const namaBarang = cells[2].textContent.toLowerCase();
                const kategori = cells[3].textContent.toLowerCase();
                const kondisi = cells[6].textContent.toLowerCase();

                const matchSearch = namaBarang.includes(searchValue);
                const matchKategori = kategoriValue === "" || kategori.includes(kategoriValue);
                const matchKondisi = kondisiValue === "" || kondisi.includes(kondisiValue);

                rows[i].style.display = (matchSearch && matchKategori && matchKondisi) ? "" : "none";

                // Hapus dulu class merah (jika ada)
                rows[i].classList.remove("baris-merah");

                // Ambil nilai awal dan sekarang (hilangkan Rp dan format)
                const nilaiAwalText = cells[7].textContent.replace(/[^\d]/g, '');
                const nilaiSekarangText = cells[8].textContent.replace(/[^\d]/g, '');


                const nilaiAwal = parseFloat(nilaiAwalText) || 0;
                const nilaiSekarang = parseFloat(nilaiSekarangText) || 0;

                if (
                    (nilaiAwal > 0 && (nilaiSekarang / nilaiAwal) * 100 <= batasPersen) ||
                    nilaiSekarang < 1000
                ) {
                    rows[i].classList.add("baris-merah");
                    rows[i].title = nilaiSekarang < 1000 ? "Nilai aset di bawah Rp 1.000" :
                        "Nilai turun melebihi batas persen";
                }

            }
        }

        searchInput.addEventListener("keyup", filterTable);
        kategoriFilter.addEventListener("change", filterTable);
        kondisiFilter.addEventListener("change", filterTable);
        batasPersenInput.addEventListener("input", filterTable);

        // Panggil saat awal juga
        window.addEventListener('load', filterTable);
        document.querySelector("#penurunanModal form").addEventListener("submit", function() {
            const btn = document.getElementById("btnPenurunan");
            const text = document.getElementById("btnPenurunanText");
            const spinner = document.getElementById("btnPenurunanSpinner");

            btn.disabled = true;
            text.classList.add("d-none");
            spinner.classList.remove("d-none");
        });
    </script>
@endsection
