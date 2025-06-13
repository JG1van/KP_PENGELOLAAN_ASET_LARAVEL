@extends('layouts.app')

@section('title', 'Penempatan Aset')
@section('page_title', 'Penempatan Aset')

@section('content')
    <form action="{{ route('penempatan.store') }}" method="POST" id="formPenempatan">
        @csrf

        <div class="mb-3">
            <label for="Tanggal_Penempatan" class="form-label fw-semibold">Tanggal</label>
            <input type="date" name="Tanggal_Penempatan" class="form-control" value="{{ date('Y-m-d') }}" readonly required>
        </div>

        <div class="mb-3">
            <label for="lokasiFilter" class="form-label fw-semibold">Pilih Lokasi</label>
            <div class="d-flex align-items-center gap-2">
                <select class="form-select" id="lokasiFilter" onchange="tampilkanTabelLokasi()">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach ($lokasi as $lok)
                        <option value="{{ $lok->Id_Lokasi }}">{{ $lok->Nama_Lokasi }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm-2" onclick="resetLokasi()">
                    <i class="fas fa-undo"></i>
                </button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="border p-3 bg-white rounded shadow-sm h-100">
                    <h5 class="text-center fw-bold mb-3">Aset Sudah Ditempatkan</h5>
                    <div class="scroll-box" style="max-height: 400px; overflow-y: auto;">
                        @foreach ($lokasi as $lok)
                            <table class="table table-bordered table-hover align-middle text-center lokasi-tabel d-none"
                                id="lokasi-{{ $lok->Id_Lokasi }}">
                                <thead>
                                    <tr>
                                        <th style="width: 20%;">ID Aset</th>
                                        <th style="width: 60%;">Nama</th>
                                        <th style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($aset->where('penempatanTerakhir.Id_Lokasi', $lok->Id_Lokasi) as $a)
                                        <tr id="row-{{ $a->Id_Aset }}" data-id="{{ $a->Id_Aset }}">
                                            <td>
                                                {{ $a->Id_Aset }}
                                                <input type="hidden" name="penempatan[{{ $a->Id_Aset }}][Id_Aset]"
                                                    value="{{ $a->Id_Aset }}">
                                                <input type="hidden" name="penempatan[{{ $a->Id_Aset }}][Id_Lokasi]"
                                                    value="{{ $lok->Id_Lokasi }}" class="input-lokasi">
                                            </td>
                                            <td>{{ $a->Nama_Aset }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="pindahKeKanan('{{ $a->Id_Aset }}')">
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted">Belum ada penempatan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border p-3 bg-white rounded shadow-sm h-100">
                    <h5 class="text-center fw-bold mb-3">Aset Baru / Tanpa Lokasi</h5>
                    <div class="scroll-box" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">ID Aset</th>
                                    <th style="width: 30%;">Nama</th>
                                    <th style="width: 30%;">Lokasi</th>
                                    <th style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="kanan-tbody">
                                @foreach ($aset->whereNull('penempatanTerakhir.Id_Lokasi')->sortBy('Id_Aset') as $a)
                                    <tr id="row-{{ $a->Id_Aset }}" data-id="{{ $a->Id_Aset }}">
                                        <td>{{ $a->Id_Aset }}</td>
                                        <td>{{ $a->Nama_Aset }}</td>
                                        <td>
                                            <select class="form-select form-select-sm lokasi-select">
                                                <option value="">-- Pilih Lokasi --</option>
                                                @foreach ($lokasi as $lok)
                                                    <option value="{{ $lok->Id_Lokasi }}">{{ $lok->Nama_Lokasi }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="pindahKeKiri('{{ $a->Id_Aset }}')">
                                                <i class="fas fa-arrow-left"></i>
                                            </button>
                                        </td>
                                        <input type="hidden" name="penempatan[{{ $a->Id_Aset }}][Id_Aset]"
                                            value="{{ $a->Id_Aset }}">
                                        <input type="hidden" name="penempatan[{{ $a->Id_Aset }}][Id_Lokasi]"
                                            value="L00" class="input-lokasi-default">
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-add w-100 mt-4 shadow-sm" onclick="konfirmasiSimpan()">
            <i class="fas fa-save me-2"></i> Simpan Penempatan
        </button>

    </form>
@endsection

@section('js')
    <script>
        function tampilkanTabelLokasi() {
            document.querySelectorAll('.lokasi-tabel').forEach(table => table.classList.add('d-none'));
            const selectedId = document.getElementById('lokasiFilter').value;
            if (selectedId) {
                document.getElementById('lokasi-' + selectedId).classList.remove('d-none');
            }
        }

        function pindahKeKiri(asetId) {
            const row = document.getElementById('row-' + asetId);
            const lokasiSelect = row.querySelector('.lokasi-select');
            const lokasiId = lokasiSelect.value;
            if (!lokasiId) {
                alert('Pilih lokasi terlebih dahulu!');
                return;
            }

            const namaAset = row.children[1].innerText;
            const newRow = document.createElement('tr');
            newRow.id = 'row-' + asetId;
            newRow.setAttribute('data-id', asetId);

            newRow.innerHTML = `
            <td>
                ${asetId}
                <input type="hidden" name="penempatan[${asetId}][Id_Aset]" value="${asetId}">
                <input type="hidden" name="penempatan[${asetId}][Id_Lokasi]" value="${lokasiId}" class="input-lokasi">
            </td>
            <td>${namaAset}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="pindahKeKanan('${asetId}')">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </td>`;

            row.remove();
            document.querySelector(`#lokasi-${lokasiId} tbody`).appendChild(newRow);
        }

        function pindahKeKanan(asetId) {
            const row = document.getElementById('row-' + asetId);
            const kananTbody = document.getElementById('kanan-tbody');
            const namaAset = row.children[1].innerText;

            const lokasiOptions = `@foreach ($lokasi as $lok)
            <option value="{{ $lok->Id_Lokasi }}">{{ $lok->Nama_Lokasi }}</option>
        @endforeach`;

            row.innerHTML = `
            <td>${asetId}</td>
            <td>${namaAset}</td>
            <td>
                <select class="form-select form-select-sm lokasi-select">
                    <option value="">-- Pilih Lokasi --</option>
                    ${lokasiOptions}
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-success" onclick="pindahKeKiri('${asetId}')">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </td>
            <input type="hidden" name="penempatan[${asetId}][Id_Aset]" value="${asetId}">
            <input type="hidden" name="penempatan[${asetId}][Id_Lokasi]" value="L00" class="input-lokasi-default">
        `;

            kananTbody.appendChild(row);
            sortTabelKanan();
        }

        function resetLokasi() {
            const selectedId = document.getElementById('lokasiFilter').value;
            if (!selectedId) return;

            const tbodyKiri = document.querySelector(`#lokasi-${selectedId} tbody`);
            const kananTbody = document.getElementById('kanan-tbody');
            const rows = Array.from(tbodyKiri.querySelectorAll('tr'));

            rows.forEach(row => {
                const asetId = row.dataset.id;
                const namaAset = row.children[1].innerText;
                const lokasiOptions = `@foreach ($lokasi as $lok)
                <option value="{{ $lok->Id_Lokasi }}">{{ $lok->Nama_Lokasi }}</option>
            @endforeach`;

                const newRow = document.createElement('tr');
                newRow.id = 'row-' + asetId;
                newRow.setAttribute('data-id', asetId);
                newRow.innerHTML = `
                <td>${asetId}</td>
                <td>${namaAset}</td>
                <td>
                    <select class="form-select form-select-sm lokasi-select">
                        <option value="">-- Pilih Lokasi --</option>
                        ${lokasiOptions}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-success" onclick="pindahKeKiri('${asetId}')">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </td>
                <input type="hidden" name="penempatan[${asetId}][Id_Aset]" value="${asetId}">
                <input type="hidden" name="penempatan[${asetId}][Id_Lokasi]" value="L00" class="input-lokasi-default">
            `;

                kananTbody.appendChild(newRow);
                row.remove();
            });

            sortTabelKanan();
        }

        function sortTabelKanan() {
            const tbody = document.getElementById('kanan-tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort((a, b) => a.dataset.id.localeCompare(b.dataset.id, undefined, {
                numeric: true
            }));
            rows.forEach(row => tbody.appendChild(row));
        }

        function konfirmasiSimpan() {
            const modalEl = document.getElementById('globalConfirmModal');
            const modal = new bootstrap.Modal(modalEl);

            // Set pesan konfirmasi
            document.getElementById("globalConfirmMessage").innerText = "Yakin ingin menyimpan aktivitas penempatan ini?";

            // Tampilkan modal
            modal.show();

            // Hindari duplikasi listener
            const confirmBtn = document.getElementById('globalConfirmOk');
            const newButton = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newButton, confirmBtn);

            // Tambahkan listener baru
            newButton.addEventListener('click', function() {
                document.getElementById('formPenempatan').submit();
            });
        }
    </script>
@endsection
