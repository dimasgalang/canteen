<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pengajuan Permintaan Pembayaran Kantin</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10.5px;
            color: #000;
        }

        /* ── Two-column outer layout via table ── */
        .page-wrapper {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
        }

        .page-wrapper td.week-block {
            width: 50%;
            vertical-align: top;
            border: 1.5px solid #000;
        }

        /* ── Header ── */
        .doc-header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #000;
        }

        .doc-header td {
            padding: 4px 6px;
            vertical-align: middle;
        }

        .logo-cell {
            width: 44px;
        }

        .logo-cell img {
            width: 40px;
        }

        .title-cell {
            text-align: center;
        }

        .title-cell p {
            font-size: 9.5px;
            font-weight: bold;
            line-height: 1.4;
            text-transform: uppercase;
            margin: 0;
        }

        /* ── Meta (Kantin / Periode) ── */
        .doc-meta {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #000;
        }

        .doc-meta td {
            padding: 3px 6px;
            font-size: 10px;
        }

        .meta-label { font-weight: bold; width: 55px; }
        .meta-colon { width: 12px; }

        /* ── Data table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
        }

        .data-table th {
            font-weight: bold;
            background-color: #e8e8e8;
            font-size: 9.5px;
            text-transform: uppercase;
        }

        .col-no     { width: 7%;  }
        .col-hari   { width: 27%; text-align: left !important; }
        .col-scan   { width: 13%; }
        .col-tscan  { width: 13%; }
        .col-jumlah { width: 13%; }
        .col-nominal{ width: 27%; }

        .row-data td {
            height: 20px;
        }

        .row-total td {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        /* ── Signature ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1.5px solid #000;
            font-size: 10px;
        }

        .sig-table td {
            padding: 6px 8px;
            vertical-align: top;
            text-align: center;
        }

        .sig-left  { width: 38%; border-right: 1px solid #000; }
        .sig-mid   { width: 28%; border-right: 1px solid #000; }
        .sig-right { width: 34%; }

        .sig-spacer { height: 32px; }

        .sig-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 2px auto;
        }

        .sig-name { font-weight: bold; }
        .sig-role { font-size: 9.5px; }
    </style>
</head>
<body>

<table class="page-wrapper" width="100%">
    <tr>

        {{-- ══════════════════ WEEK BLOCK 1 ══════════════════ --}}
        <td class="week-block">

            {{-- Header --}}
            <table class="doc-header" width="100%">
                <tr>
                    <td class="logo-cell">
                        <img src="{{ public_path('img/Chutex.png') }}" alt="Logo">
                    </td>
                    <td class="title-cell">
                        <p>Pengajuan Permintaan Pembayaran Kantin</p>
                        <p>PT Chutex International Indonesia</p>
                    </td>
                </tr>
            </table>

            {{-- Meta --}}
            <table class="doc-meta" width="100%">
                <tr>
                    <td class="meta-label">KANTIN</td>
                    <td class="meta-colon">:</td>
                    <td>{{ $kantin ?? '' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">PERIODE</td>
                    <td class="meta-colon">:</td>
                    <td>{{ $periode1 ?? '' }}</td>
                </tr>
            </table>

            {{-- Data table --}}
            @php
                $w1scan = 0; $w1tidak = 0; $w1jumlah = 0; $w1nominal = 0;
            @endphp
            <table class="data-table" width="100%">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-hari">Hari, Tanggal</th>
                        <th class="col-scan">Jumlah Scan</th>
                        <th class="col-tscan">Tidak Scan</th>
                        <th class="col-jumlah">Jumlah</th>
                        <th class="col-nominal">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 7; $i++)
                        @php
                            $row = $week1[$i] ?? null;
                            if ($row) {
                                $w1scan    += $row['jumlah_scan'];
                                $w1tidak   += $row['tidak_scan'];
                                $w1jumlah  += $row['jumlah'];
                                $w1nominal += $row['nominal'];
                            }
                        @endphp
                        <tr class="row-data">
                            <td>{{ $i + 1 }}</td>
                            <td style="text-align:left; padding-left:5px;">{{ $row['hari_tanggal'] ?? '' }}</td>
                            <td>{{ $row ? $row['jumlah_scan'] : '' }}</td>
                            <td>{{ $row ? $row['tidak_scan'] : '' }}</td>
                            <td>{{ $row ? $row['jumlah'] : '' }}</td>
                            <td>{{ $row ? 'Rp ' . number_format($row['nominal'], 0, ',', '.') : '' }}</td>
                        </tr>
                    @endfor
                    <tr class="row-total">
                        <td colspan="2" style="text-align:left; padding-left:5px;">TOTAL</td>
                        <td>{{ $w1scan ?: '' }}</td>
                        <td>{{ $w1tidak ?: '' }}</td>
                        <td>{{ $w1jumlah ?: '' }}</td>
                        <td>{{ $w1nominal ? 'Rp ' . number_format($w1nominal, 0, ',', '.') : '' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Signature --}}
            <table class="sig-table" width="100%">
                <tr>
                    <td class="sig-left">
                        Mengetahui,
                        <div class="sig-spacer"></div>
                        <div class="sig-line"></div>
                        <div class="sig-name">Rakhmat Budiyono</div>
                        <div class="sig-role">HRD Manager</div>
                    </td>
                    <td class="sig-mid"></td>
                    <td class="sig-right">
                        Yang mengajukan,
                        <div class="sig-spacer"></div>
                        <div class="sig-line"></div>
                        <div>(............................)</div>
                        <div class="sig-role">Pihak Kantin</div>
                    </td>
                </tr>
            </table>

        </td>{{-- /week-block 1 --}}


        {{-- ══════════════════ WEEK BLOCK 2 ══════════════════ --}}
        <td class="week-block">

            {{-- Header --}}
            <table class="doc-header" width="100%">
                <tr>
                    <td class="logo-cell">
                        <img src="{{ public_path('img/Chutex.png') }}" alt="Logo">
                    </td>
                    <td class="title-cell">
                        <p>Pengajuan Permintaan Pembayaran Kantin</p>
                        <p>PT Chutex International Indonesia</p>
                    </td>
                </tr>
            </table>

            {{-- Meta --}}
            <table class="doc-meta" width="100%">
                <tr>
                    <td class="meta-label">KANTIN</td>
                    <td class="meta-colon">:</td>
                    <td>{{ $kantin ?? '' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">PERIODE</td>
                    <td class="meta-colon">:</td>
                    <td>{{ $periode2 ?? '' }}</td>
                </tr>
            </table>

            {{-- Data table --}}
            @php
                $w2scan = 0; $w2tidak = 0; $w2jumlah = 0; $w2nominal = 0;
            @endphp
            <table class="data-table" width="100%">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-hari">Hari, Tanggal</th>
                        <th class="col-scan">Jumlah Scan</th>
                        <th class="col-tscan">Tidak Scan</th>
                        <th class="col-jumlah">Jumlah</th>
                        <th class="col-nominal">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 7; $i++)
                        @php
                            $row = $week2[$i] ?? null;
                            if ($row) {
                                $w2scan    += $row['jumlah_scan'];
                                $w2tidak   += $row['tidak_scan'];
                                $w2jumlah  += $row['jumlah'];
                                $w2nominal += $row['nominal'];
                            }
                        @endphp
                        <tr class="row-data">
                            <td>{{ $i + 1 }}</td>
                            <td style="text-align:left; padding-left:5px;">{{ $row['hari_tanggal'] ?? '' }}</td>
                            <td>{{ $row ? $row['jumlah_scan'] : '' }}</td>
                            <td>{{ $row ? $row['tidak_scan'] : '' }}</td>
                            <td>{{ $row ? $row['jumlah'] : '' }}</td>
                            <td>{{ $row ? 'Rp ' . number_format($row['nominal'], 0, ',', '.') : '' }}</td>
                        </tr>
                    @endfor
                    <tr class="row-total">
                        <td colspan="2" style="text-align:left; padding-left:5px;">TOTAL</td>
                        <td>{{ $w2scan ?: '' }}</td>
                        <td>{{ $w2tidak ?: '' }}</td>
                        <td>{{ $w2jumlah ?: '' }}</td>
                        <td>{{ $w2nominal ? 'Rp ' . number_format($w2nominal, 0, ',', '.') : '' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Signature --}}
            <table class="sig-table" width="100%">
                <tr>
                    <td class="sig-left">
                        Mengetahui,
                        <div class="sig-spacer"></div>
                        <div class="sig-line"></div>
                        <div class="sig-name">Rakhmat Budiyono</div>
                        <div class="sig-role">HRD Manager</div>
                    </td>
                    <td class="sig-mid"></td>
                    <td class="sig-right">
                        Yang mengajukan,
                        <div class="sig-spacer"></div>
                        <div class="sig-line"></div>
                        <div>(............................)</div>
                        <div class="sig-role">Pihak Kantin</div>
                    </td>
                </tr>
            </table>

        </td>{{-- /week-block 2 --}}

    </tr>
</table>

</body>
</html>
