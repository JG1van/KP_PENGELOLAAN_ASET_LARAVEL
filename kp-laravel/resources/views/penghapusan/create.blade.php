@extends('layouts.app')

@section('title', 'Tambah Aktivitas Penghapusan')
@section('page_title', 'Tambah Aktivitas Penghapusan Aset')

@section('content')
    @if ($errors->any())
        <div class="alert custom-alert-2 alert-dismissible fade show mx-3">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif

    @if ($asets->count())
        <div class="alert custom-alert-1 alert-dismissible fade show mx-3">
            Ditemukan {{ $asets->count() }} aset yang memenuhi kriteria untuk dihapus.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @else
        <div class="alert custom-alert-2 alert-dismissible fade show mx-3">
            Tidak ada aset yang memenuhi kriteria untuk dihapus.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="formPenghapusan" action="{{ route('penghapusan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tanggal Penghapusan</label>
            <input type="text" name="Tanggal_Hapus" class="form-control" id="tanggalPenghapusan" readonly>
        </div>

        <div class="mb-4">
            <label class="form-label">Upload Dokumen Penghapusan (PDF/JPG/PNG)</label>
            <input type="file" name="Dokumen_Penghapusan" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>

        <div class="mb-3">
            <p class="text-muted">
                Aset yang ditampilkan adalah aset berstatus aktif dengan kondisi
                <strong>rusak berat</strong>, <strong>hilang</strong> atau <strong>diremajakan</strong>.
            </p>
        </div>

        @if ($asets->count())
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Aset</th>
                            <th>Nama Aset</th>
                            <th>Kondisi Terakhir</th>
                            <th>Pilih untuk Dihapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asets as $index => $aset)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $aset->Id_Aset }}</td>
                                <td>{{ $aset->Nama_Aset }}</td>
                                <td>{{ $aset->Kondisi }}</td>
                                <td>
                                    <input type="checkbox" name="aset_terpilih[]" value="{{ $aset->Id_Aset }}" checked>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-add w-100" onclick="konfirmasiSubmitPenghapusan()"
                @if ($asets->isEmpty()) disabled @endif>
                <i class="fas fa-save me-2"></i> Simpan Aktivitas Penghapusan
            </button>
        </div>
    </form>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const today = new Date();
            const formatted = today.toISOString().split("T")[0];
            document.getElementById("tanggalPenghapusan").value = formatted;
        });

        function konfirmasiSubmitPenghapusan() {
            const modal = new bootstrap.Modal(document.getElementById("globalConfirmModal"));
            const message = document.getElementById("globalConfirmMessage");
            const okBtn = document.getElementById("globalConfirmOk");

            message.textContent = "Yakin ingin menyimpan aktivitas penghapusan aset ini?";

            // Reset tampilan tombol OK sebelum dipakai
            okBtn.disabled = false;
            okBtn.innerHTML = "Ya, Simpan";

            // Hapus semua event listener lama dengan cloneNode
            const newOkBtn = okBtn.cloneNode(true);
            okBtn.parentNode.replaceChild(newOkBtn, okBtn);

            // Pasang event listener baru
            newOkBtn.addEventListener("click", function() {
                newOkBtn.disabled = true;
                newOkBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';

                document.getElementById("formPenghapusan").submit();
            });

            modal.show();
        }
    </script>
@endsection
