@csrf
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Penjualan</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div> 
        <div class="modal-body"> 
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID</th> 
                    <td>{{ $detail_penjualan->detail_id }}</td> 
                </tr> 
                <tr> 
                    <th>Kode Penjualan</th> 
                    <td>{{ $detail_penjualan->penjualan->penjualan_kode }}</td> 
                </tr> 
                <tr> 
                    <th>Nama Barang</th> 
                    <td>{{ $detail_penjualan->barang->barang_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Harga</th> 
                    <td>{{ $detail_penjualan->barang->harga_jual }}</td> 
                </tr>  
                <tr> 
                    <th>Jumlah</th> 
                    <td>{{ $detail_penjualan->jumlah }}</td> 
                </tr> 
                <tr> 
                    <th>Total</th> 
                    <td>{{ $detail_penjualan->total }}</td> 
                </tr> 
            </table> 
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" data-dismiss="modal" class="btn btn-default">Kembali</button>
                <a href="{{ url('/penjualan/print_struk') }}" target="_blank" class="btn btn-primary">Cetak Struk</a>
            </div>
        </div> 
    </div> 
</div> 