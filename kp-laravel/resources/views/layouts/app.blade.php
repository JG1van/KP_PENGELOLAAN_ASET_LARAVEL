<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'KP SISTEM PENGELOLAAN ASET')</title>

    <!-- CSS -->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

</head>

<body>
    <nav>
        <!-- Sidebar Offcanvas (Mobile) -->
        <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarMobile">
            <div class="offcanvas-header flex-column">
                <div class="logo-container mb-2 w-20 text-center align-items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo" />
                </div>
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <h5 class="offcanvas-title mb-0">Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
            </div>

            <div class="offcanvas-body p-0 text-start">
                <ul class="list-unstyled ps-0">
                    <li class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="text-white d-block py-2 px-3">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                        <a href="{{ route('kategori.index') }}" class="text-white d-block py-2 px-3">
                            <i class="fas fa-th-large me-2"></i>Kategori
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('aset.*') ? 'active' : '' }}">
                        <a href="{{ route('aset.index') }}" class="text-white d-block py-2 px-3">
                            <i class="fas fa-box me-2"></i>Aset
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('penerimaan.*') ? 'active' : '' }}">
                        <a href="{{ route('penerimaan.index') }}" class="text-white d-block py-2 px-3">
                            <i class="fas fa-download me-2"></i>Penerimaan
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('pengecekan.*') ? 'active' : '' }}">
                        <a href="{{ route('pengecekan.index') }}" class="text-white d-block py-2 px-3">
                            <i class="fas fa-qrcode me-2"></i>Pengecekan
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('penghapusan.*') ? 'active' : '' }}">
                        <a href="{{ route('penghapusan.index') }}" class="text-white d-block py-2 px-3">
                            <i class="fas fa-trash me-2"></i>Penghapusan
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                        <a href="{{ route('pengaturan.index') }}" class="text-white d-block py-2 px-3">
                            <i class="fas fa-cog me-2"></i>Pengaturan
                        </a>
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="mt-5 p-0">
                            @csrf
                            <button type="submit"
                                class="text-white d-block py-2 px-3 w-100 text-start bg-transparent border-0">
                                <i class="fas fa-sign-out-alt me-2"></i>Keluar
                            </button>
                        </form>
                    </li>
                </ul>

            </div>

        </div>

        <!-- Sidebar Desktop -->
        <div class="sidebar d-none d-md-block bg-dark text-white p-3 position-fixed text-start">
            <div class="logo-container mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo" />
            </div>
            <ul class="list-unstyled">
                <li class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="text-white d-block py-2 px-3">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                </li>
                <li class="{{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    <a href="{{ route('kategori.index') }}" class="text-white d-block py-2 px-3">
                        <i class="fas fa-th-large me-2"></i>Kategori
                    </a>
                </li>
                <li class="{{ request()->routeIs('aset.*') ? 'active' : '' }}">
                    <a href="{{ route('aset.index') }}" class="text-white d-block py-2 px-3">
                        <i class="fas fa-box me-2"></i>Aset
                    </a>
                </li>
                <li class="{{ request()->routeIs('penerimaan.*') ? 'active' : '' }}">
                    <a href="{{ route('penerimaan.index') }}" class="text-white d-block py-2 px-3">
                        <i class="fas fa-download me-2"></i>Penerimaan
                    </a>
                </li>
                <li class="{{ request()->routeIs('pengecekan.*') ? 'active' : '' }}">
                    <a href="{{ route('pengecekan.index') }}" class="text-white d-block py-2 px-3">
                        <i class="fas fa-qrcode me-2"></i>Pengecekan
                    </a>
                </li>
                <li class="{{ request()->routeIs('penghapusan.*') ? 'active' : '' }}">
                    <a href="{{ route('penghapusan.index') }}" class="text-white d-block py-2 px-3">
                        <i class="fas fa-trash me-2"></i>Penghapusan
                    </a>
                </li>
                <li class="{{ request()->routeIs('pengaturan.*') ? 'active' : '' }} ">
                    <a href="{{ route('pengaturan.index') }}" class="text-white d-block py-2 px-3">
                        <i class="fas fa-cog me-2 "></i>Pengaturan
                    </a>
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="mt-5 p-0">
                        @csrf
                        <button type="submit"
                            class="text-white d-block py-2 px-3 w-100 text-start bg-transparent border-0">
                            <i class="fas fa-sign-out-alt me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>

        </div>

    </nav>

    <main class="main-content p-3">
        <div class="header d-flex justify-content-between align-items-center mb-4">
            <div class="d-md-none">
                <button class="btn btn-outline-dark" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <h1>@yield('page_title', 'Dashboard')</h1>
            <a href="{{ route('pengaturan.profil.index') }}" class="tu-info text-black text-decoration-none">
                <span>{{ Auth::user()->name }}</span>
                <i class="fas fa-user-circle"></i>
            </a>

        </div>
        {{-- Notifikasi Success --}}
        @if (session('success'))
            <div class="alert custom-alert-1 alert-dismissible fade show mx-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert custom-alert-1 alert-dismissible fade show mx-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Konten Halaman --}}
        @yield('content')

        <!-- Modal Konfirmasi Global -->
        <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="globalConfirmModalLabel">Konfirmasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body" id="globalConfirmMessage">Apakah Anda yakin?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm-2" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-sm-1" id="globalConfirmOk">Ya, Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>

    </main>


    {{-- Script JS Global --}}
    @yield('js')
    <script>
        function showGlobalConfirm(message, onConfirmCallback) {
            document.getElementById('globalConfirmMessage').innerText = message;
            const confirmModal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));
            confirmModal.show();

            // Reset tombol event listener
            const confirmButton = document.getElementById('globalConfirmYes');
            const newButton = confirmButton.cloneNode(true);
            confirmButton.parentNode.replaceChild(newButton, confirmButton);

            newButton.addEventListener('click', function() {
                onConfirmCallback();
                confirmModal.hide();
            });
        }
    </script>
    <script>
        function showConfirmModal(message, onConfirm) {
            const modal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));
            const msg = document.getElementById('globalConfirmMessage');
            const okBtn = document.getElementById('globalConfirmOk');

            msg.textContent = message;

            // Bersihkan listener lama
            const newBtn = okBtn.cloneNode(true);
            okBtn.parentNode.replaceChild(newBtn, okBtn);

            newBtn.addEventListener('click', function() {
                modal.hide();
                if (typeof onConfirm === 'function') onConfirm();
            });

            modal.show();
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
