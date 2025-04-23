<!DOCTYPE html> 
<html> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
    <style> 
        body{ 
            font-family: "Times New Roman", Times, serif; 
            margin: 6px 20px 5px 20px; 
            line-height: 15px; 
        } 
        table { 
            width:100%;  
            border-collapse: collapse; 
        } 
        td, th { 
            padding: 4px 3px; 
        } 
        th{ 
            text-align: left; 
        } 
        .d-block{ 
            display: block; 
        } 
        img{ 
            width: auto; 
            height: 80px; 
            max-width: 150px; 
            max-height: 150px; 
            margin: 0 0 8px 18px; 
        } 
        .text-right { 
            text-align: right; 
        } 
        .text-center { 
            text-align: center; 
        } 
        .p-1{ 
            padding: 5px 1px 5px 1px; 
        } 
        .font-10{ 
            font-size: 10pt; 
        } 
        .font-11{ 
            font-size: 11pt; 
        } 
        .font-12{ 
            font-size: 12pt; 
        } 
        .font-13{ 
            font-size: 13pt; 
        } 
        .border-bottom-header{ 
            border-bottom: 1px solid; 
        } 
        .border-all, .border-all th, .border-all td{ 
            border: 1px solid; 
        } 
        span.font-bold{ 
            font-weight: bold; 
            margin-bottom: 2px; 
        }
    </style> 
</head> 
<body> 
    <table class="border-bottom-header"> 
        <tr> 
            <td width="15%" class="text-center"><img src="logo_polinema.jpg"></td> 
            <td width="85%"> 
                <span class="text-center d-block font-11 font-bold">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</span> 
                <span class="text-center d-block font-13 font-bold">POLITEKNIK NEGERI MALANG</span> 
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span> 
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101105, 0341-404420, Fax. (0341) 404420</span> 
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span> 
            </td> 
        </tr> 
    </table> 

    <h3 class="text-center">LAPORAN DATA PENJUALAN</h3>

    <table class="border-all">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Kode Penjualan</th>
                <th>Tanggal Penjualan</th>
                <th>Pembeli</th>
                <th class="text-right">Total Pembayaran</th>
                <th class="text-center">Detail Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan as $p)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $p->penjualan_kode }}</td>
                <td>{{ \Carbon\Carbon::parse($p->penjualan_tanggal)->format('d-m-Y') }}</td>
                <td>{{ $p->pembeli }}</td>
                <td class="text-right">
                    {{ number_format($p->penjualan_detail->sum(fn($item) => $item->harga * $item->jumlah), 0, ',', '.') }}
                </td>
                <td class="text-center">
                    <table class="border-all">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Jumlah</th>
                                <th class="text-right">Sub Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($p->penjualan_detail as $detail)
                            <tr>
                                <td>{{ $detail->barang->barang_kode }}</td>
                                <td>{{ $detail->barang->barang_nama }}</td>
                                <td class="text-right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                                <td class="text-right">{{ $detail->jumlah }}</td>
                                <td class="text-right">{{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>