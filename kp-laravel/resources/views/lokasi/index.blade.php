@extends('layouts.app')

@section('title', 'Lokasi Aset')
@section('page_title', 'Lokasi Aset')

@section('content')

    {{-- Modal Gagal Hapus --}}
    <div class="modal fade" id="modalGagalHapus" tabindex="-1" aria-labelledby="modalGagalHapusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalGagalHapusLabel">Gagal Menghapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{ session('error') ?? 'Lokasi sedang digunakan dan tidak bisa dihapus.' }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Pencarian dan Tambah --}}
    <div class="row g-2 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label">Pencarian</label>
            <input id="searchInput" type="text" class="form-control" placeholder="Cari Nama Lokasi...." />
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalLokasi">
                <i class="fas fa-plus me-2"></i>Tambah Lokasi
            </button>
        </div>
    </div>

    {{-- Tabel Lokasi --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle" id="lokasiTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Lokasi</th>
                    <th>Nama Lokasi</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->Id_Lokasi }}</td>
                        <td>{{ $item->Nama_Lokasi }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Tombol Edit --}}
                                <a href="#" class="btn btn-sm-1" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->Id_Lokasi }}">Edit</a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('lokasi.destroy', $item->Id_Lokasi) }}" method="POST"
                                    id="delete-form-{{ $item->Id_Lokasi }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm-2"
                                        onclick="konfirmasiGlobal('Yakin ingin menghapus lokasi ini?', function() { submitDeleteLokasi('{{ $item->Id_Lokasi }}') })">
                                        <span id="textHapus{{ $item->Id_Lokasi }}">Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">Tidak ada data lokasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Lokasi --}}
    <div class="modal fade" id="modalLokasi" tabindex="-1" aria-labelledby="modalLokasiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambahLokasi" class="modal-content" method="POST" action="{{ route('lokasi.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLokasiLabel">Tambah Lokasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lokasi</label>
                        <input type="text" class="form-control" name="Nama_Lokasi" required
                            placeholder="Masukkan nama lokasi" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Lokasi --}}
    @foreach ($data as $item)
        <div class="modal fade" id="editModal{{ $item->Id_Lokasi }}" tabindex="-1"
            aria-labelledby="editModalLabel{{ $item->Id_Lokasi }}" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content bg-white" method="POST"
                    action="{{ route('lokasi.update', $item->Id_Lokasi) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel{{ $item->Id_Lokasi }}">Edit Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lokasi</label>
                            <input type="text" class="form-control" name="Nama_Lokasi" value="{{ $item->Nama_Lokasi }}"
                                required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-add w-100">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("searchInput");
            const table = document.getElementById("lokasiTable");

            searchInput.addEventListener("keyup", function() {
                const filter = searchInput.value.toLowerCase();
                const rows = table.getElementsByTagName("tr");

                for (let i = 1; i < rows.length; i++) {
                    const cell = rows[i].getElementsByTagName("td")[2];
                    if (cell) {
                        const txtValue = cell.textContent || cell.innerText;
                        rows[i].style.display = txtValue.toLowerCase().includes(filter) ? "" : "none";
                    }
                }
            });

            document.querySelectorAll("form.modal-content").forEach(form => {
                form.addEventListener("submit", function() {
                    const tombol = form.querySelector("button[type='submit']");
                    if (tombol) {
                        tombol.disabled = true;
                        tombol.innerHTML =
                            '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
                    }
                });
            });
        });

        function submitDeleteLokasi(id) {
            const form = document.getElementById(`delete-form-${id}`);
            form.submit();
        }

        function konfirmasiGlobal(pesan, callback) {
            const modal = new bootstrap.Modal(document.getElementById("globalConfirmModal"));
            document.getElementById("globalConfirmMessage").textContent = pesan;
            const okBtn = document.getElementById("globalConfirmOk");

            okBtn.disabled = false;
            okBtn.innerHTML = 'OK';

            okBtn.onclick = function() {
                okBtn.disabled = true;
                okBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
                callback();
            };

            modal.show();
        }
    </script>
@endsection
