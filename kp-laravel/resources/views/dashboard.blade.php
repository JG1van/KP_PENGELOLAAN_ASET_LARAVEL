@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Pengelolaan Aset')

@section('content')
    <div class="container my-4">
        <div class="row mb-3 align-items-stretch g-2">
            <div class="col-md-8">

                <div
                    class="card card-custom p-4 d-flex flex-column flex-md-row justify-content-between align-items-center border shadow-sm text-center">
                    {{-- Gambar kiri --}}
                    <div class="d-none d-md-block px-2">
                        <img src="{{ asset('images/ilustrasi-selamat-datang-1.png') }}" alt="Ilustrasi Kiri"
                            style="height: 120px; max-width: 240px;">
                    </div>

                    {{-- Teks tengah --}}
                    <div class="flex-grow-1 px-3">
                        <h4 class="fw-bold mb-2">Halo, {{ Auth::user()->name }} 👋</h4>
                        <p class="fs-6 text-muted mb-0">
                            Selamat datang di <span class="fw-semibold text-dark">Sistem Pengelolaan Aset</span>
                        </p>
                    </div>

                    {{-- Gambar kanan --}}
                    <div class="d-none d-md-block px-2">
                        <img src="{{ asset('images/ilustrasi-selamat-datang-2.png') }}" alt="Ilustrasi Kanan"
                            style="height: 120px; max-width: 240px;">
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="row g-2" style="max-height: 600px; overflow-y: auto;">
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px; max-height: 160px;">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Aset Aktif</h6>
                            <h4 class="fw-bold text-dark fs-5">{{ $asetAktif }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px; max-height: 160px;">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Aset Tidak Aktif</h6>
                            <h4 class="fw-bold text-dark fs-5">{{ $asetTidakAktif }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px; max-height: 160px;">
                            <i class="bi bi-cash-coin text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Nilai Aset Aktif</h6>
                            <h4 class="fw-bold text-dark fs-6">
                                Rp {{ number_format($totalNilaiAsetAktif, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card p-3 text-center border" style="min-height: 140px; max-height: 160px;">
                            <i class="bi bi-wallet2 text-secondary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 text-truncate">Nilai Aset Tidak Aktif</h6>
                            <h4 class="fw-bold text-dark fs-6">
                                Rp {{ number_format($totalNilaiAsetTidakAktif, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>


        </div>


        <div class="row mb-3">

            <div class="col-md-5">
                <div class="card p-3 border d-flex flex-row align-items-center mb-2" style="gap: 15px;">
                    <i class="bi bi-bar-chart-line text-info" style="font-size: 2.5rem;"></i>
                    <div>
                        <div class="fw-semibold">Total Nilai Aset Saat Ini</div>
                        <div class="fw-bold text-dark fs-6">Rp {{ number_format($totalNilaiAsetSaatIni, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card p-3 border d-flex flex-row align-items-center mb-2" style="gap: 15px;">
                    <i class="bi bi-box-seam text-primary" style="font-size: 2.5rem;"></i>
                    <div>
                        <div class="fw-semibold">Total Aset</div>
                        <div class="fw-bold text-dark fs-5">{{ $totalAset }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card p-3 border d-flex flex-row align-items-center mb-2" style="gap: 15px;">
                    <i class="bi bi-coin text-warning" style="font-size: 2.5rem;"></i>
                    <div>
                        <div class="fw-semibold">Total Nilai Awal Aset</div>
                        <div class="fw-bold text-dark fs-6">Rp {{ number_format($totalNilaiAwalAset, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Baris 3: Grafik -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card p-2 border">
                    <h6 class="text-center"><i class="bi bi-pie-chart-fill"></i> Grafik Kondisi Aset</h6>
                    <div style="height: 250px;">
                        <canvas id="kondisiChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card p-2 border">
                    <h6 class="text-center"><i class="bi bi-pie-chart-fill"></i> Grafik Jumlah Aset per Lokasi</h6>
                    <div style="height: 250px;">
                        <canvas id="penempatanChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <div class="card p-2 border">
                    <h6 class="text-center"><i class="bi bi-bar-chart-fill"></i> Grafik Kategori Aset</h6>
                    <canvas id="kategoriChart" style="height:200px !important;"></canvas>
                </div>
            </div>

        </div>
        <div class="TabelPerTahun">
            <div class="card p-2">
                <h6 class="mb-3"><i class="bi bi-calendar-event"></i> Jumlah Aset per Tahun</h6>
                <div style="max-height: 260px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-bordered w-100 table-hover text-center align-middle mb-0">
                            <thead class="align-middle">
                                <tr>
                                    <th>Tahun</th>
                                    <th>Jumlah Aset</th>
                                    <th>Nilai Aset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataAsetPerTahun as $tahun => $data)
                                    <tr>
                                        <td>{{ $tahun }}</td>
                                        <td>{{ $data['jumlah'] }}</td>
                                        <td>{{ number_format($data['nilai_awal'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('js')
    <script>
        const kondisiChart = document.getElementById("kondisiChart").getContext("2d");
        new Chart(kondisiChart, {
            type: "doughnut",
            data: {
                labels: {!! json_encode($dataKondisi->keys()) !!},
                datasets: [{
                    data: {!! json_encode($dataKondisi->values()) !!},
                    backgroundColor: ["#2ecc71", "#f1c40f", "#e67e22", "#e74c3c", "#5DADE2"],
                    borderWidth: 5,
                    borderColor: "#ffffff"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            color: "#333",
                            font: {
                                size: 14,
                                weight: "bold"
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed} Aset`;
                            }
                        }
                    }
                }
            }
        });
        // Fungsi untuk menghasilkan warna acak dengan saturasi tinggi
        function generateColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const hue = Math.floor(360 * Math.random());
                const saturation = 70 + Math.random() * 30; // 70%–100%
                const lightness = 45 + Math.random() * 15; // 45%–60%
                colors.push(`hsl(${hue}, ${saturation}%, ${lightness}%)`);
            }
            return colors;
        }

        const dataPenempatan = @json($dataPenempatan);
        const ctxPenempatan = document.getElementById('penempatanChart').getContext('2d');
        const lokasiLabels = Object.keys(dataPenempatan);
        const lokasiData = Object.values(dataPenempatan);
        const dynamicColors = generateColors(lokasiLabels.length); // 👈 Ini yang berubah

        new Chart(ctxPenempatan, {
            type: 'doughnut',
            data: {
                labels: lokasiLabels,
                datasets: [{
                    label: 'Jumlah Aset',
                    data: lokasiData,
                    backgroundColor: dynamicColors, // 👈 Pakai warna dinamis
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed} Aset`;
                            }
                        }
                    }
                }
            }
        });

        const kategoriChart = document.getElementById("kategoriChart").getContext("2d");
        new Chart(kategoriChart, {
            type: "bar",
            data: {
                labels: {!! json_encode($dataKategori->keys()) !!},
                datasets: [{
                    label: "Jumlah Aset",
                    data: {!! json_encode($dataKategori->values()) !!},
                    backgroundColor: "#B05B3B",
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: "#333",
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: "#eee"
                        }
                    },
                    x: {
                        ticks: {
                            color: "#333",
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
