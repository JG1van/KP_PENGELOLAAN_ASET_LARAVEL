@extends('layouts.app')

@section('title', 'QR Code Aset')
@section('page_title', 'QR Code Aset')

@section('content')
    <h3 class="text-center">ID : {{ $penerimaan->Id_Penerimaan }}</h3>

    @php
        $chunks = collect($asetList)->chunk(9); // Pastikan collection & bagi per 9
    @endphp

    <div class="alas p-3">
        @foreach ($chunks as $index => $chunk)
            <div class="page-break mb-5">
                <div class="container">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                        {{-- QR Code Aset --}}
                        @foreach ($chunk as $aset)
                            <div class="col text-center">
                                <div class="qr-slot">
                                    {!! QrCode::size(180)->generate($aset->Id_Aset) !!}
                                    <div class="mt-2 fw-bold">{{ $aset->Id_Aset }}</div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Slot kosong untuk menjaga layout 3x3 --}}
                        @for ($i = 0; $i < 9 - $chunk->count(); $i++)
                            <div class="col text-center">
                                <div class="qr-slot"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <a href="{{ route('penerimaan.qr.pdf', $penerimaan->Id_Penerimaan) }}" class="btn btn-add w-100 mt-5">
        <i class="fas fa-file-pdf me-1"></i> Export PDF
    </a>
@endsection

@push('styles')
    <style>
        .qr-slot {
            border: 1px dashed #ccc;
            padding: 1rem;
            min-height: 220px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
@endpush
