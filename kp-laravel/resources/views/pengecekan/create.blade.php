@extends('layouts.app')

@section('title', 'Tambah Aktivitas Pengecekan')
@section('page_title', 'Tambah Aktivitas Pengecekan')

@section('content')
    <style>
        #cameraContainer {
            width: 300px;
            height: 200px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        #qr-reader {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none !important;
            border-radius: 0;
        }

        .ikon-peringatan {
            color: red;
            font-size: 1.5rem;
            margin-left: 4px;
        }
    </style>

    <form action="{{ route('pengecekan.store') }}" method="POST" id="formPengecekan">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tanggal Pengecekan</label>
            <input type="text" name="Tanggal_Pengecekan" class="form-control" id="tanggalPengecekan" readonly />
        </div>

        <div class="mb-3 text-center mx-auto" style="max-width: 300px">
            <label for="cameraSelect" class="form-label fw-semibold">Pilih Kamera</label>
            <select id="cameraSelect" class="form-select"></select>
        </div>

        <div id="cameraContainer">
            <div id="qr-reader" class="rounded" style="width: 300px;"></div>
        </div>

        <div class="mb-3 mt-3">
            <label class="form-label">Pencarian Manual</label>
            <input id="searchInput" type="text" class="form-control" placeholder="Cari ID/Nama Aset..." />
        </div>

        <div class="table-responsive">
            <table class="table table-bordered w-100 table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th colspan="6" class="text-end align-middle">Batas Penurunan Nilai (%)</th>
                        <th colspan="1">
                            <input id="batasPersenInput" type="number" value="5" min="1" max="100"
                                class="form-control form-control-sm text-center">
                        </th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>ID Aset</th>
                        <th>Nama Aset</th>
                        <th>Nilai Awal</th>
                        <th>Nilai Sekarang</th>
                        <th>Kondisi Terakhir</th>
                        <th>Kondisi Sekarang</th>
                    </tr>
                </thead>
                <tbody id="daftarAset">
                    @forelse ($asetAktif as $index => $aset)
                        @php
                            $nilaiAwal = $aset->Nilai_Aset_Awal;
                            $nilaiSekarang = optional($aset->PenurunanTerbaru)->Nilai_Saat_Ini ?? 0;
                            $persentase = $nilaiAwal > 0 ? ($nilaiSekarang / $nilaiAwal) * 100 : 100;
                        @endphp
                        <tr data-id="{{ $aset->Id_Aset }}" data-nilai-awal="{{ $nilaiAwal }}"
                            data-nilai-sekarang="{{ $nilaiSekarang }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $aset->Id_Aset }}</td>
                            <td>{{ $aset->Nama_Aset }}</td>
                            <td>Rp {{ number_format($nilaiAwal, 0, ',', '.') }}</td>
                            <td>
                                Rp {{ number_format($nilaiSekarang, 0, ',', '.') }}
                                @if ($nilaiAwal > 0 && $persentase <= 5)
                                    <i class="fas fa-exclamation-triangle ikon-peringatan" title="Nilai aset rendah"></i>
                                @endif
                            </td>
                            <td>{{ $aset->Kondisi }}</td>
                            <td>
                                <select name="kondisi[{{ $aset->Id_Aset }}]" class="form-select">
                                    <option value="">Pilih</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak sedang">Rusak Sedang</option>
                                    <option value="rusak berat">Rusak Berat</option>
                                    <option value="diremajakan">Diremajakan</option>
                                </select>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted">Tidak ada data aset.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-add w-100 mt-3" id="btnTriggerConfirm"
            @if ($asetAktif->isEmpty()) disabled @endif>
            <i class="fas fa-save me-2"></i> Selesai
        </button>
    </form>
@endsection

@section('js')
    <script src="https://unpkg.com/html5-qrcode@2.3.9/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const today = new Date().toISOString().split("T")[0];
            document.getElementById("tanggalPengecekan").value = today;

            // Filter pencarian
            document.getElementById("searchInput").addEventListener("input", function() {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll("#daftarAset tr").forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(keyword) ? "" : "none";
                });
            });

            // QR Scanner setup
            let html5QrCode;
            let currentCameraId = null;

            function startScanner(cameraId) {
                const qrRegion = document.getElementById("qr-reader");
                qrRegion.innerHTML = "";

                html5QrCode = new Html5Qrcode("qr-reader");
                html5QrCode.start(
                    cameraId, {
                        fps: 10,
                        qrbox: 250
                    },
                    decodedText => {
                        const row = document.querySelector(`#daftarAset tr[data-id="${decodedText}"]`);
                        if (row) {
                            row.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            row.classList.add('table-success');
                            setTimeout(() => row.classList.remove('table-success'), 5000);
                        } else {
                            alert("ID Aset tidak ditemukan atau tidak aktif.");
                        }
                    },
                    errorMessage => {
                        /* silent */
                    }
                ).catch(err => console.error("Scanner error:", err));
            }

            Html5Qrcode.getCameras().then(cameras => {
                const cameraSelect = document.getElementById("cameraSelect");
                cameras.forEach(camera => {
                    const option = document.createElement("option");
                    option.value = camera.id;
                    option.text = camera.label || `Kamera ${camera.id}`;
                    cameraSelect.appendChild(option);
                });

                if (cameras.length > 0) {
                    currentCameraId = cameras[0].id;
                    cameraSelect.value = currentCameraId;
                    startScanner(currentCameraId);
                }

                cameraSelect.addEventListener("change", function() {
                    if (html5QrCode) {
                        html5QrCode.stop().then(() => {
                            startScanner(this.value);
                        });
                    }
                });
            });

            // Highlight nilai rendah jika <= batas persen
            document.getElementById("batasPersenInput").addEventListener("input", function() {
                const batas = parseFloat(this.value);
                document.querySelectorAll("#daftarAset tr").forEach(row => {
                    const nilaiAwal = parseFloat(row.dataset.nilaiAwal || 0);
                    const nilaiSekarang = parseFloat(row.dataset.nilaiSekarang || 0);
                    const ikon = row.querySelector(".ikon-peringatan");

                    if (nilaiAwal > 0 && (nilaiSekarang / nilaiAwal) * 100 <= batas) {
                        if (!ikon) {
                            const cell = row.cells[4];
                            const i = document.createElement("i");
                            i.className = "fas fa-exclamation-triangle ikon-peringatan";
                            i.title = "Nilai aset rendah";
                            cell.appendChild(i);
                        }
                    } else {
                        if (ikon) ikon.remove();
                    }
                });
            });
            // Tambahkan warna jika select dipilih manual
            document.querySelectorAll('#daftarAset select.form-select').forEach(select => {
                select.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (this.value) {
                        row.classList.add('baris-terisi');
                    } else {
                        row.classList.remove('baris-terisi');
                    }
                });
            });

            document.getElementById("btnTriggerConfirm").addEventListener("click", function() {
                const modalEl = document.getElementById("globalConfirmModal");
                if (!modalEl) {
                    alert("Modal konfirmasi tidak ditemukan!");
                    return;
                }
                const modal = new bootstrap.Modal(modalEl);
                document.getElementById("globalConfirmMessage").innerText =
                    "Simpan aktivitas pengecekan ini?";
                modal.show();

                document.getElementById("globalConfirmOk").onclick = function() {
                    const okButton = document.getElementById("globalConfirmOk");
                    okButton.disabled = true;
                    okButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';

                    document.getElementById("formPengecekan").submit();
                };
            });
        });
    </script>
@endsection
