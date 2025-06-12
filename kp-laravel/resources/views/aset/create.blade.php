@extends('layouts.app')

@section('title', 'Tambah Aset')
@section('page_title', 'Tambah Aset')

@section('content')
    <form action="{{ route('aset.store') }}" method="POST" enctype="multipart/form-data" id="formAset">
        @csrf

        <div class="mb-3">
            <label for="tanggal_penerimaan" class="form-label required">Tanggal Penerimaan</label>
            <input type="date" class="form-control" id="tanggal_penerimaan" name="tanggal_penerimaan" required readonly />
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nama" class="form-label required">Nama Penyedia</label>
                <input type="text" class="form-control" id="nama" name="nama" required autocomplete="off"
                    placeholder="Masukkan Nama" />
            </div>
            <div class="col-md-6">
                <label for="telepon" class="form-label required">Nomor Telepon Penyedia</label>
                <input type="text" class="form-control" id="telepon" name="telepon" required autocomplete="off"
                    placeholder="Masukkan Nomor Telepon" maxlength="15" minlength="10" />
            </div>
        </div>

        <h5>Daftar Barang</h5>
        <div class="table-responsive mb-2">
            <table class="table table-bordered table-hover text-center align-middle" id="tabelBarang">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Nilai Satuan</th>
                        <th>Total Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

        <button class="btn btn-add mb-3" type="button" data-bs-toggle="modal" data-bs-target="#modalBarang">
            Tambah Barang
        </button>

        <div class="mb-3">
            <label for="dokumen" class="form-label">Upload Dokumen Penerimaan (PDF/JPG/PNG)</label>
            <input type="file" class="form-control" id="dokumen" name="dokumen" accept=".pdf,.jpg,.jpeg,.png"
                required />
        </div>

        <div class="alas mb-2"></div>
        <input type="hidden" name="barang_json" id="barangJson" />
        <button type="button" class="btn btn-add w-100" onclick="validasiFormSebelumSubmit()">
            Selesai
        </button>
    </form>

    <!-- Modal Tambah Barang -->
    <div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="modalBarangLabel" aria-hidden="true">
        <div class="modal-dialog alas">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="alertNilai" class="alert alert-danger d-none" role="alert">
                        Nilai satuan melebihi batas maksimum Rp 9.999.999.999,99.
                    </div>

                    <div class="mb-2">
                        <label class="form-label required">Nama Aset</label>
                        <input type="text" class="form-control" id="namaBarang" placeholder="Masukkan Nama Aset" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label required">Kategori</label>
                        <select class="form-select" id="kategoriBarang">
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat->Id_Kategori }}">{{ $kat->Nama_Kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label required">Jumlah</label>
                        <input type="number" id="jumlahBarang" min="1" max="999" value="1"
                            class="form-control" />

                    </div>
                    <div class="mb-2">
                        <label class="form-label required">Nilai Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text"
                                style="background-color: #753422; border: 4px solid #753422; color: white;">Rp</span>
                            <input type="text" class="form-control" id="nilaiSatuan" placeholder="Masukkan Nilai Aset" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-add w-100" id="tambahBarangBtn">Simpan Barang</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputTanggal = document.getElementById("tanggal_penerimaan");
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;
            inputTanggal.value = todayStr;
            inputTanggal.max = todayStr;

            const daftarBarang = [];
            const tabelBody = document.querySelector("#tabelBarang tbody");

            function renderTabel() {
                tabelBody.innerHTML = "";

                if (daftarBarang.length === 0) {
                    tabelBody.innerHTML = `<tr><td colspan="7" class="text-muted">Belum ada data.</td></tr>`;
                    return;
                }

                daftarBarang.forEach((item, index) => {
                    const total = item.jumlah * item.nilai;
                    const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama}</td>
                        <td>${item.kategori_text}</td>
                        <td>${item.jumlah}</td>
                        <td>Rp ${item.nilai.toLocaleString("id-ID")}</td>
                        <td>Rp ${total.toLocaleString("id-ID")}</td>
                        <td><button class="btn btn-sm btn-danger" onclick="hapusBarang(${index})">Hapus</button></td>
                    </tr>
                `;
                    tabelBody.innerHTML += row;
                });

                document.getElementById("barangJson").value = JSON.stringify(daftarBarang);
            }
            renderTabel();
            window.hapusBarang = function(index) {
                if (confirm("Yakin ingin menghapus barang ini?")) {
                    daftarBarang.splice(index, 1);
                    renderTabel();
                }
            };

            document.getElementById("tambahBarangBtn").addEventListener("click", function() {
                const nama = document.getElementById("namaBarang").value.trim();
                const kategoriSelect = document.getElementById("kategoriBarang");
                const kategori = kategoriSelect.value;
                const kategoriText = kategoriSelect.options[kategoriSelect.selectedIndex]?.text || '';
                const jumlah = parseInt(document.getElementById("jumlahBarang").value);
                const nilaiRaw = document.getElementById("nilaiSatuan").value.replace(/[^\d]/g, "");
                const nilai = parseFloat(nilaiRaw);

                const batasMaksimum = 9999999999.99;
                const alertBox = document.getElementById("alertNilai");
                alertBox.classList.add("d-none");

                if (!nama) return alert("Nama aset harus diisi.");
                if (!kategori) return alert("Kategori aset harus dipilih.");
                if (isNaN(jumlah) || jumlah < 1 || jumlah > 999) return alert("Jumlah harus antara 1–999.");
                if (isNaN(nilai) || nilai < 1) return alert(
                    "Nilai satuan harus berupa angka lebih dari 0.");
                if (nilai > batasMaksimum) {
                    alertBox.textContent = "Nilai satuan melebihi batas maksimum Rp 9.999.999.999,99.";
                    alertBox.classList.remove("d-none");
                    return;
                }

                daftarBarang.push({
                    nama,
                    kategori,
                    kategori_text: kategoriText,
                    jumlah,
                    nilai
                });
                renderTabel();

                // Reset
                document.getElementById("namaBarang").value = "";
                document.getElementById("kategoriBarang").value = "";
                document.getElementById("jumlahBarang").value = "1";
                document.getElementById("nilaiSatuan").value = "";

                bootstrap.Modal.getInstance(document.getElementById("modalBarang")).hide();
            });

            document.getElementById("nilaiSatuan").addEventListener("input", function(e) {
                let value = e.target.value.replace(/[^\d]/g, "");
                e.target.value = value ? parseInt(value, 10).toLocaleString("id-ID") : "";
            });

            document.getElementById("telepon").addEventListener("input", function() {
                this.value = this.value.replace(/\D/g, "").slice(0, 15);
            });

            window.validasiFormSebelumSubmit = function() {
                const tanggal = document.getElementById("tanggal_penerimaan").value.trim();
                const nama = document.getElementById("nama").value.trim();
                const telepon = document.getElementById("telepon").value.trim();
                const dokumen = document.getElementById("dokumen").files.length;
                const barang = JSON.parse(document.getElementById("barangJson").value || "[]");

                if (!tanggal) return alert("Tanggal penerimaan harus diisi.");
                if (!nama) return alert("Nama penyedia harus diisi.");
                if (!telepon || telepon.length < 10 || telepon.length > 15)
                    return alert("Nomor telepon harus terdiri dari 10 hingga 15 digit.");
                if (dokumen === 0) return alert("Dokumen penerimaan harus diunggah.");
                if (barang.length === 0) return alert("Tambahkan setidaknya satu barang ke dalam daftar.");

                // Modal konfirmasi
                const modalEl = document.getElementById("globalConfirmModal");
                if (!modalEl) {
                    alert("Modal konfirmasi tidak ditemukan!");
                    return;
                }

                const modal = new bootstrap.Modal(modalEl);
                document.getElementById("globalConfirmMessage").innerText = "Simpan aset ini?";
                modal.show();

                document.getElementById("globalConfirmOk").onclick = function() {
                    const okButton = document.getElementById("globalConfirmOk");
                    okButton.disabled = true;
                    okButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';

                    document.getElementById("formAset").submit();
                };

            };
        });
    </script>
@endsection
