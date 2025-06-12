@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Pengelolaan Aset')

@section('content')
    <div class="Ringkasan Cepat">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card p-3 text-center">
                    <h6>Total Aset</h6>
                    <h4>{{ $totalAset }}</h4>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="card p-3 text-center">
                    <h6>Aset Aktif</h6>
                    <h4>{{ $asetAktif }}</h4>
                </div>
            </div>
            <div class="col-6 col-md-6">
                <div class="card p-3 text-center">
                    <h6>Aset Tidak Aktif </h6>
                    <h4>{{ $asetTidakAktif }}</h4>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card p-3 text-center">
                    <h6>Total Nilai Aset Saat Ini</h6>
                    <h4>Rp {{ number_format($totalNilaiAsetSaatIni, 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card p-3 text-center">
                    <h6>Total Nilai Aset Aktif Saat Ini</h6>
                    <h4>Rp {{ number_format($totalNilaiAsetAktif, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 text-center">
                    <h6>Total Nilai Aset Tidak Aktif Saat Ini</h6>
                    <h4>Rp {{ number_format($totalNilaiAsetTidakAktif, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card p-3 text-center">
                    <h6>Total Nilai Awal Aset</h6>
                    <h4>Rp {{ number_format($totalNilaiAwalAset, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="Grafik align-items-center mb-4">
        <div class="row">
            <div class="col-md-4" style="min-height: 350px;">
                <canvas id="kondisiChart" style="height:300px !important;"></canvas>
            </div>
            <div class="col-md-8" style="min-height: 350px;">
                <canvas id="kategoriChart" style="height:300px !important;"></canvas>
            </div>
        </div>
    </div>

    <div class="TabelPerTahun">
        <div class="card p-3">
            <h6 class="mb-3">Jumlah Aset per Tahun</h6>
            <div style="max-height: 260px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle mb-0">
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
