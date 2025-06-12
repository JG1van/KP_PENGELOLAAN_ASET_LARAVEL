@extends('layouts.app')

@section('title', 'Detail Penerimaan')
@section('page_title', 'Detail Penerimaan')

@section('content')
    <div class="alas p-3">
        <form id="form-edit-penerimaan" action="{{ route('penerimaan.update', $penerimaan->Id_Penerimaan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-2">
                <label>ID Penerimaan</label>
                <input type="text" class="form-control" value="{{ $penerimaan->Id_Penerimaan }}" readonly>
            </div>
            <div class="mb-2">
                <label>Tanggal</label>
                <input type="date" class="form-control" name="Tanggal_Terima" value="{{ $penerimaan->Tanggal_Terima }}"
                    required readonly>
            </div>
            <div class="mb-2">
                <label>Keterangan</label>
                <textarea name="Keterangan" class="form-control" rows="6" required>{{ $penerimaan->Keterangan }}</textarea>
            </div>
        </form>

        @if ($penerimaan->Dokumen_Penerimaan)
            @foreach ($files as $file)
                <div class="mt-3">
                    <button onclick="window.open('{{ $file['secure_url'] }}', '_blank')" class="btn btn-sm-2 w-100">
                        Lihat Dokumen: {{ basename($file['public_id']) }}
                    </button>
                </div>
            @endforeach
            <div class="mt-3 text-center">
                <a href="{{ route('penerimaan.qr', $penerimaan->Id_Penerimaan) }}" class="btn btn-sm-2 w-100">
                    <i class="fas fa-qrcode me-1"></i> Cetak QR Code Aset
                </a>
            </div>
        @endif
    </div>

    <h4 class="mt-4">Daftar Aset yang Diterima</h4>
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Aset</th>
                    <th>Nama Aset</th>
                    <th>Kategori</th>
                    <th>Kondisi</th>
                    <th>Nilai Awal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penerimaan->detailPenerimaan as $i => $detail)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $detail->aset->Id_Aset }}</td>
                        <td>{{ $detail->aset->Nama_Aset }}</td>
                        <td>{{ $detail->aset->kategori->Nama_Kategori ?? '-' }}</td>
                        <td>{{ $detail->aset->Kondisi }}</td>
                        <td>Rp {{ number_format($detail->aset->Nilai_Aset_Awal, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('aset.show', $detail->aset->Id_Aset) }}" class="btn btn-sm-1">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-muted">Tidak ada aset.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tombol Aksi --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <form id="form-hapus-penerimaan" action="{{ route('penerimaan.destroy', $penerimaan->Id_Penerimaan) }}"
                method="POST">
                @csrf
                @method('DELETE')
                <button id="btnHapusPenerimaan" type="button" class="btn btn-sm-1 w-100"
                    onclick="konfirmasiGlobal('Yakin ingin menghapus penerimaan ini dan seluruh aset terkait?', submitHapusPenerimaan)">
                    <span id="textHapus">Hapus Penerimaan</span>
                    <span class="d-none" id="spinnerHapus"><i class="fas fa-spinner fa-spin me-2"></i> Menghapus...</span>
                </button>
            </form>
        </div>
        <div class="col-md-6">
            <button id="btnEditPenerimaan" type="button" class="btn btn-add w-100"
                onclick="konfirmasiGlobal('Simpan perubahan penerimaan?', submitEditPenerimaan)">
                <span id="textEdit">Simpan Perubahan</span>
                <span class="d-none" id="spinnerEdit"><i class="fas fa-spinner fa-spin me-2"></i> Memproses...</span>
            </button>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function konfirmasiGlobal(pesan, callback) {
            const modal = new bootstrap.Modal(document.getElementById("globalConfirmModal"));
            document.getElementById("globalConfirmMessage").textContent = pesan;

            const okBtn = document.getElementById("globalConfirmOk");
            okBtn.disabled = false;
            okBtn.innerHTML = 'OK';

            okBtn.onclick = function() {
                okBtn.disabled = true;
                okBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
                callback(); // Jalankan fungsi sesuai tombolnya
            };

            modal.show();
        }

        function submitEditPenerimaan() {
            const btn = document.getElementById('btnEditPenerimaan');
            document.getElementById('textEdit').classList.add('d-none');
            document.getElementById('spinnerEdit').classList.remove('d-none');
            btn.disabled = true;
            document.getElementById('form-edit-penerimaan').submit();
        }

        function submitHapusPenerimaan() {
            const btn = document.getElementById('btnHapusPenerimaan');
            document.getElementById('textHapus').classList.add('d-none');
            document.getElementById('spinnerHapus').classList.remove('d-none');
            btn.disabled = true;
            document.getElementById('form-hapus-penerimaan').submit();
        }
    </script>
@endsection
