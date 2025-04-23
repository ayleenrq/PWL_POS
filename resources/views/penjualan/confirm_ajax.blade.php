@empty($penjualan)
    <div id="myModal" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
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
    <form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div id="myModal" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                        Anda yakin ingin menghapus data penjualan ini?
                    </div>
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
                
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function(){
            $('#form-delete').validate({
                rules: {},
                submitHandler: function(form){
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response){
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                }).then(function() {
                                    location.reload();   // 🔥 reload halaman setelah user klik OK
                                });
                                dataPenjualan.ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty