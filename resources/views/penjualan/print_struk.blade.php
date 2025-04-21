<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan</title>
    <style>
        body {
            width: 200px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0 auto;
            padding: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; }
        td { vertical-align: top; }
    </style>
</head>
<body>

    <div class="text-center">
        <strong>TOKO AYLEEN</strong><br>
        Jl. Mana Aja No.123<br>
        Malang, Jawa Timur<br>
        ----------------------------
    </div>

    <table>
        <tr>
            <td>Kode Transaksi</td>
            <td class="text-right">{{ $penjualan->penjualan_kode }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="text-right">{{ $penjualan->user->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pembeli</td>
            <td class="text-right">{{ $penjualan->pembeli }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="text-right">{{ $penjualan->penjualan_tanggal }}</td>
        </tr>
    </table>

    <div class="line"></div>
    <table>
        @foreach($penjualan->penjualanDetail as $detail)
            <tr>
                <td colspan="2">{{ $detail->barang->barang_nama }}</td>
            </tr>
            <tr>
                <td>{{ $detail->jumlah }} x {{ number_format($detail->harga,0,',','.') }}</td>
                <td class="text-right">{{ number_format($detail->total,0,',','.') }}</td>
            </tr>
        @endforeach
    </table>
    <div class="line"></div>

    <table>
        <tr>
            <td><strong>Total</strong></td>
            <td class="text-right">
                <strong>
                    {{ number_format($penjualan->penjualanDetail->sum('total'),0,',','.') }}
                </strong>
            </td>
        </tr>
    </table>

    <div class="text-center" style="margin-top:10px;">
        ~ Terima Kasih ~<br>
        Barang yang sudah dibeli tidak bisa dikembalikan.
    </div>
</body>
</html>
