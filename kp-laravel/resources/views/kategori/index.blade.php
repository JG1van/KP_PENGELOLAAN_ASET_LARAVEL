@extends('layouts.app')

@section('title', 'Kategori Aset')
@section('page_title', 'Kategori Aset')

@section('content')

    {{-- Modal Gagal Hapus --}}
    <div class="modal fade" id="modalGagalHapus" tabindex="-1" aria-labelledby="modalGagalHapusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalGagalHapusLabel">Gagal Menghapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    {{ session('error') ?? 'Kategori sedang digunakan dan tidak bisa dihapus.' }}
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
            <input id="searchInput" type="text" class="form-control" placeholder="Cari Nama Kategori...." />
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-add w-100" data-bs-toggle="modal" data-bs-target="#modalKategori">
                <i class="fas fa-plus me-2"></i>Tambah Kategori
            </button>
        </div>
    </div>

    {{-- Tabel Kategori --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center align-middle" id="kategoriTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Kategori</th>
                    <th>Nama Kategori</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->Id_Kategori }}</td>
                        <td>{{ $item->Nama_Kategori }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Tombol Edit --}}
                                <a href="#" class="btn btn-sm-1" data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $item->Id_Kategori }}">Edit</a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('kategori.destroy', $item->Id_Kategori) }}" method="POST"
                                    id="delete-form-{{ $item->Id_Kategori }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm-2"
                                        onclick="konfirmasiGlobal('Yakin ingin menghapus kategori ini?', function() { submitDeleteKategori('{{ $item->Id_Kategori }}') })">
                                        <span id="textHapus{{ $item->Id_Kategori }}">Hapus</span>

                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">Tidak ada data kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Kategori --}}
    <div class="modal fade" id="modalKategori" tabindex="-1" aria-labelledby="modalKategoriLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambahKategori" class="modal-content" method="POST" action="{{ route('kategori.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalKategoriLabel">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="namaKategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" name="Nama_Kategori" id="namaKategori" required
                            placeholder="Masukkan nama kategori" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Kategori --}}
    @foreach ($data as $item)
        <div class="modal fade" id="editModal{{ $item->Id_Kategori }}" tabindex="-1"
            aria-labelledby="editModalLabel{{ $item->Id_Kategori }}" aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content bg-white" method="POST"
                    action="{{ route('kategori.update', $item->Id_Kategori) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel{{ $item->Id_Kategori }}">Edit Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" name="Nama_Kategori"
                                value="{{ $item->Nama_Kategori }}" required />
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
            const table = document.getElementById("kategoriTable");

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

            // Tambah kategori - disable tombol submit saat diklik
            const formTambah = document.getElementById("formTambahKategori");
            formTambah?.addEventListener("submit", function() {
                const tombol = formTambah.querySelector("button[type='submit']");
                tombol.disabled = true;
                tombol.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
            });

            // Edit kategori - disable tombol submit saat diklik
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

        function submitDeleteKategori(idKategori) {
            const btn = document.querySelector(`#delete-form-${idKategori} button`);
            btn.disabled = true;
            document.getElementById(`delete-form-${idKategori}`).submit();
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
                callback(); // Jalankan fungsi sesuai konfirmasi
            };

            modal.show();
        }
    </script>
@endsection
