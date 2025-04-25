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
                    <td>{{ $stok->stok_id }}</td> 
                </tr> 
                <tr> 
                    <th>Tanggal/Waktu</th> 
                    <td>{{ $stok->tanggal }}</td> 
                </tr> 
                <tr> 
                    <th>Barang</th> 
                    <td>{{ $stok->barang->barang_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Jenis Stok</th> 
                    <td>{{ $stok->jenis_stok }}</td> 
                </tr> 
                <tr> 
                    <th>Supplier</th> 
                    <td>{{ $stok->supplier->supplier_nama }}</td> 
                </tr> 
                <tr> 
                    <th>User</th> 
                    <td>{{ $stok->user->nama }}</td> 
                </tr> 
                <tr> 
                    <th>Stok</th> 
                    <td>{{ $stok->stok_jumlah }}</td> 
                </tr> 
            </table> 
            <button type="button" data-dismiss="modal" class="btn btn-default">Kembali</button>
        </div> 
    </div> 
</div> 