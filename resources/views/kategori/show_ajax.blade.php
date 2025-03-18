@csrf
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Detail Data Kategori</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div> 
        <div class="modal-body"> 
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID</th> 
                    <td>{{ $kategori->kategori_id }}</td> 
                </tr> 
                <tr> 
                    <th>Kode Kategori</th> 
                    <td>{{ $kategori->kategori_kode }}</td> 
                </tr> 
                <tr> 
                    <th>Kategori Nama</th> 
                    <td>{{ $kategori->kategori_nama }}</td> 
                </tr> 
            </table> 
            <button type="button" data-dismiss="modal" class="btn btn-default">Kembali</button>
        </div> 
    </div> 
</div> 