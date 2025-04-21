@csrf
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Stok</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div> 
        <div class="modal-body"> 
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID</th> 
                    <td>{{ $riwayatstok->riwayat_stok_id }}</td> 
                </tr> 
                <tr> 
                    <th>Tanggal/Waktu</th> 
                    <td>{{ $riwayatstok->tanggal }}</td> 
                </tr> 
                <tr> 
                    <th>Barang</th> 
                    <td>{{ $riwayatstok->barang->barang_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Supplier</th> 
                    <td>{{ $riwayatstok->supplier->supplier_nama }}</td> 
                </tr> 
                <tr> 
                    <th>User</th> 
                    <td>{{ $riwayatstok->user->nama }}</td> 
                </tr> 
                <tr> 
                    <th>Stok</th> 
                    <td>{{ $riwayatstok->stok_jumlah }}</td> 
                </tr> 
            </table> 
            <button type="button" data-dismiss="modal" class="btn btn-default">Kembali</button>
        </div> 
    </div> 
</div> 