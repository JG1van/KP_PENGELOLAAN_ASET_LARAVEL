@extends('layouts.app')

@section('title', 'Detail Aset')
@section('page_title', 'Detail Aset')
@section('content')

    <div class="alas p-3 ">
        <form action="{{ route('aset.update.detail', $aset->Id_Aset) }}" method="POST" id="formUpdateAset">
            @csrf
            @method('PUT')

            <div class="mb-3 text-center">
                <label>ID Aset</label>
                <input type="text" class="form-control text-center" value="{{ $aset->Id_Aset }}" readonly>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label>Nama Aset</label>
                    <input type="text" class="form-control" name="Nama_Aset" value="{{ $aset->Nama_Aset }}" required>
                </div>
                <div class="col-6">
                    <label>Kategori</label>
                    <select name="Id_Kategori" class="form-select " required>
                        @foreach ($kategori as $kat)
                            <option value="{{ $kat->Id_Kategori }}"
                                {{ $aset->Id_Kategori == $kat->Id_Kategori ? 'selected' : '' }}>
                                {{ $kat->Nama_Kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label>Penempatan</label>
                    <input type="text" class="form-control" name="Penempatan"
                        value="{{ old('Penempatan', $aset->Penempatan) }}" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label>Status</label>
                    <input type="text" class="form-control " value="{{ $aset->STATUS }}" readonly>
                </div>
                <div class="col-6">
                    <label>Kondisi</label>
                    <input type="text" class="form-control " value="{{ $aset->Kondisi }}" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label>Tanggal Masuk</label>
                    <input type="text" class="form-control"
                        value="{{ optional(optional($aset->detailPenerimaan)->penerimaan)->Tanggal_Terima ?? '-' }}"
                        readonly>
                </div>

                <div class="col-6">
                    <label>Nilai Aset Awal</label>
                    <input type="text" class="form-control"
                        value="Rp {{ number_format($aset->Nilai_Aset_Awal, 0, ',', '.') }}" readonly>
                </div>
            </div>
        </form>
        <div class="mt-3 text-end">
            <button type="button" class="btn btn-add w-100"
                onclick="showGlobalConfirm('Apakah Anda yakin ingin menyimpan perubahan data aset ini?', () => document.getElementById('formUpdateAset').submit())">
                Simpan Perubahan
            </button>
        </div>
    </div>
    <h4 class="mt-4">Riwayat Penurunan</h4>
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Tahun</th>
                    <th>Nilai Setelah Penurunan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($aset->penurunans->sortBy('Tahun') as $penurunan)
                    <tr>
                        <td>{{ $penurunan->Tahun }}</td>
                        <td>Rp {{ number_format($penurunan->Nilai_Saat_Ini, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-muted">Belum ada data penurunan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>




@endsection

@section('js')
    <style>
        .is-invalid {
            border-color: #dc3545;
        }
    </style>

    <script>
        document.getElementById("globalConfirmOk").onclick = function() {
            const form = document.getElementById("formUpdateAset");
            const requiredFields = form.querySelectorAll("[required]");
            let valid = true;
            let firstEmpty = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    if (!firstEmpty) firstEmpty = field;
                    field.classList.add("is-invalid");
                } else {
                    field.classList.remove("is-invalid");
                }
            });

            if (!valid) {
                alert("Harap isi semua field yang wajib diisi.");
                if (firstEmpty) firstEmpty.focus();
                return;
            }

            form.submit();
        };
    </script>
@endsection
