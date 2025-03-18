@csrf
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Barang</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div> 
        <div class="modal-body"> 
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID</th> 
                    <td>{{ $barang->barang_id }}</td> 
                </tr> 
                <tr> 
                    <th>Kategori Barang</th> 
                    <td>{{ $barang->kategori->kategori_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Kode Barang</th> 
                    <td>{{ $barang->barang_kode }}</td> 
                </tr> 
                <tr> 
                    <th>Nama Barang</th> 
                    <td>{{ $barang->barang_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Harga Beli</th> 
                    <td>{{ $barang->harga_beli }}</td> 
                </tr> 
                <tr> 
                    <th>Harga Jual</th> 
                    <td>{{ $barang->harga_jual }}</td> 
                </tr> 
            </table> 
            <button type="button" data-dismiss="modal" class="btn btn-default">Kembali</button>
        </div> 
    </div> 
</div> 