@extends('layouts.app')

@section('title', 'Pengaktifan Aset')
@section('page_title', 'Daftar Aset Tidak Aktif')

@section('content')
    {{-- Filter --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari ID atau Nama Aset...">
        </div>
    </div>

    {{-- Tabel Aset Tidak Aktif --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle" id="tabelAset">
            <thead class="align-middle">
                <tr>
                    <th>No</th>
                    <th>ID Aset</th>
                    <th>Nama Aset</th>
                    <th>Kondisi Terakhir</th>
                    <th>Status</th>
                    <th>Tanggal Terima</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $aset)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="id-aset">{{ $aset->Id_Aset }}</td>
                        <td class="nama-aset">{{ $aset->Nama_Aset }}</td>
                        <td class="kondisi-aset">{{ ucfirst($aset->Kondisi) }}</td>
                        <td>{{ ucfirst($aset->STATUS) }}</td>
                        <td>{{ optional(optional($aset->detailPenerimaan)->penerimaan)->Tanggal_Terima ?? '-' }}</td>
                        <td>
                            <button class="btn btn-sm btn-add btnAktifkan" data-id="{{ $aset->Id_Aset }}"
                                data-nama="{{ $aset->Nama_Aset }}" data-kondisi="{{ $aset->Kondisi }}"
                                data-action="{{ route('pengaturan.pengaktifan.aktifkan', ['id' => $aset->Id_Aset]) }}"
                                data-bs-toggle="modal" data-bs-target="#modalAktifkan">
                                Aktifkan
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-muted">Tidak ada aset tidak aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Form Pengaktifan --}}
    <div class="modal fade" id="modalAktifkan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="#" enctype="multipart/form-data" id="formAktifkan" class="modal-content">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title">Form Pengaktifan Aset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>ID Aset</label>
                        <input type="text" class="form-control" id="modalIdAset" readonly>
                    </div>
                    <div class="mb-2">
                        <label>Nama Aset</label>
                        <input type="text" class="form-control" id="modalNamaAset" readonly>
                    </div>
                    <div class="mb-2">
                        <label>Kondisi Sebelumnya</label>
                        <input type="text" class="form-control" id="modalKondisi" readonly>
                    </div>
                    <div class="mb-2">
                        <label>Dokumen Pendukung</label>
                        <input type="file" name="dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    <div class="mb-2">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" placeholder="Keterangan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-add w-100" type="submit">Simpan dan Aktifkan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("searchInput");
            const rows = document.querySelectorAll("#tabelAset tbody tr");

            function filterTable() {
                const searchVal = searchInput.value.toLowerCase();
                rows.forEach(row => {
                    const id = row.querySelector(".id-aset").textContent.toLowerCase();
                    const nama = row.querySelector(".nama-aset").textContent.toLowerCase();
                    const matchSearch = id.includes(searchVal) || nama.includes(searchVal);
                    row.style.display = matchSearch ? "" : "none";
                });
            }

            searchInput.addEventListener("input", filterTable);

            const btns = document.querySelectorAll(".btnAktifkan");
            const modalId = document.getElementById("modalIdAset");
            const modalNama = document.getElementById("modalNamaAset");
            const modalKondisi = document.getElementById("modalKondisi");
            const formAktifkan = document.getElementById("formAktifkan");

            btns.forEach(btn => {
                btn.addEventListener("click", () => {
                    modalId.value = btn.dataset.id;
                    modalNama.value = btn.dataset.nama;
                    modalKondisi.value = btn.dataset.kondisi;
                    formAktifkan.action = btn.dataset.action;

                    // Reset tombol submit di awal buka modal
                    const tombol = formAktifkan.querySelector("button[type='submit']");
                    tombol.disabled = false;
                    tombol.innerHTML = 'Simpan dan Aktifkan';
                });
            });

            // Anti submit ganda saat form dikirim
            formAktifkan.addEventListener("submit", function() {
                const tombol = formAktifkan.querySelector("button[type='submit']");
                tombol.disabled = true;
                tombol.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
            });
        });
    </script>
@endsection
