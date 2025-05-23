@csrf
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Supplier</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div> 
        <div class="modal-body"> 
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID</th> 
                    <td>{{ $supplier->supplier_id }}</td> 
                </tr> 
                <tr> 
                    <th>Kode Supplier</th> 
                    <td>{{ $supplier->supplier_kode }}</td> 
                </tr> 
                <tr> 
                    <th>Supplier Nama</th> 
                    <td>{{ $supplier->supplier_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Alamat Supplier</th> 
                    <td>{{ $supplier->supplier_alamat }}</td> 
                </tr> 
            </table> 
            <button type="button" data-dismiss="modal" class="btn btn-default">Kembali</button>
        </div> 
    </div> 
</div> 