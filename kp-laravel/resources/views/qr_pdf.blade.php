<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .qr-table {
            width: 100%;
            border-collapse: collapse;
        }

        .qr-cell {
            width: 33.33%;
            height: 240px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .qr-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .qr-image {
            margin-bottom: 8px;
        }

        .qr-id {
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <h3 style="text-align: center; margin-bottom: 30px;">
        QR Code Aset - Penerimaan: {{ $penerimaan->Id_Penerimaan }}
    </h3>

    @php
        // Pastikan $asetList adalah Collection
        $chunks = collect($asetList)->chunk(9);
    @endphp

    @foreach ($chunks as $chunk)
        @php
            // Tetap pakai object, jangan toArray, agar bisa akses ->Id_Aset
            $filledChunk = $chunk->pad(9, null)->values();

        @endphp

        <table class="qr-table page-break">
            @for ($row = 0; $row < 3; $row++)
                <tr>
                    @for ($col = 0; $col < 3; $col++)
                        @php
                            $index = $row * 3 + $col;
                            $aset = $filledChunk[$index];
                        @endphp
                        <td class="qr-cell">
                            @if ($aset)
                                <div class="qr-wrapper">
                                    @php
                                        $qrSvg = base64_encode(
                                            QrCode::format('svg')->size(120)->generate($aset->Id_Aset),
                                        );
                                    @endphp
                                    <img class="qr-image" src="data:image/svg+xml;base64,{{ $qrSvg }}"
                                        width="120" height="120" alt="QR Aset {{ $aset->Id_Aset }}">
                                    <div class="qr-id">{{ $aset->Id_Aset }}</div>
                                </div>
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor
        </table>
    @endforeach

</body>

</html>
