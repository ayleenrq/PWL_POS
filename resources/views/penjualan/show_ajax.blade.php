@empty($penjualan)
<div id="mymodal" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan
            </div>
            <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
    <div id="mymodal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-left col-3">User :</th>
                        <td class="col-9">{{ $penjualan->user->nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-left col-3">Kode Penjualan :</th>
                        <td class="col-9">{{ $penjualan->penjualan_kode }}</td>
                    </tr>
                    <tr>
                        <th class="text-left col-3">Pembeli :</th>
                        <td class="col-9">{{ $penjualan->pembeli }}</td>
                    </tr>
                    <tr>
                        <th class="text-left col-3">Tanggal :</th>
                        <td class="col-9">{{ $penjualan->penjualan_tanggal }}</td>
                    </tr>
                </table>

                <hr>
                <h5>Detail Penjualan</h5>
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; $total = 0; @endphp
                        @foreach ($penjualan->penjualan_detail as $item)
                            @php 
                                $harga = $item->barang->harga_jual;
                                $jumlah = $item->jumlah;
                                $subtotal = $harga * $jumlah;
                                $total += $item->harga * $item->jumlah;
                            @endphp
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $item->barang->barang_nama }}</td>
                                <td>Rp {{ number_format($harga, 0, ',', '.') }}</td>
                                <td>{{ $jumlah }}</td>
                                <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-right">Total Harga:</td>
                            <td>{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Kembali</button>
                    <a href="{{ url('/penjualan/' . $penjualan->penjualan_id . '/print_struk') }}" class="btn btn-primary">Cetak Struk</a>
                </div>
            </div>
        </div>
    </div>
@endempty