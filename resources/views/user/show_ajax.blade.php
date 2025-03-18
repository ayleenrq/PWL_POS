@csrf
<div id="modal-master" class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Detail Data User</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> 
        </div> 
        <div class="modal-body"> 
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID</th> 
                    <td>{{ $user->user_id }}</td> 
                </tr> 
                <tr> 
                    <th>Level</th> 
                    <td>{{ $user->level->level_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Username</th> 
                    <td>{{ $user->username }}</td> 
                </tr> 
                <tr> 
                    <th>Nama</th> 
                    <td>{{ $user->nama }}</td> 
                </tr> 
                <tr> 
                    <th>Password</th> 
                    <td>********</td> 
                </tr> 
            </table> 
            <button type="button" data-dismiss="modal" class="btn btn-default">Kembali</button>
        </div> 
    </div> 
</div> 