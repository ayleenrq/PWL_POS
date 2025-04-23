@empty($barang)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
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
                <a href="{{ url('/barang') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/penjualan/'.$penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Penjualan</label>
                        <input value="{{ $penjualan->penjualan_kode }}" type="text" name="penjualan_kode" id="penjualan_kode" class="form-control" required>
                        <small id="error-penjualan_kode" class="error-text form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Pembeli</label>
                        <input value="{{ $penjualan->pembeli }}" type="text" name="pembeli" id="pembeli" class="form-control" required>
                        <small id="error-pembeli" class="error-text form-text text-danger"></small>
                    </div>

                    <hr>
                    <h5>Detail Penjualan</h5>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-success btn-sm" id="btn-add-item">
                                <i class="fa fa-plus"></i> Tambah Barang
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="detail-table">
                            <thead>
                            <tr>
                                <th>Barang</th>
                                <th width="150">Harga (Rp)</th>
                                <th width="100">Jumlah</th>
                                <th width="150">Subtotal (Rp)</th>
                                <th width="50">Aksi</th>
                            </tr>
                            </thead>
                            <tbody id="detail-items">
                            @foreach($penjualan->penjualanDetail as $detail)
                                <tr class="detail-row">
                                    <td>
                                        <select name="barang_id[]" class="form-control barang-select" required>
                                            <option value="">- Pilih Barang -</option>
                                            @foreach ($barang as $b)
                                                <option value="{{ $b->barang_id }}"
                                                        data-harga="{{ $b->harga_jual }}"
                                                    {{ $detail->barang_id == $b->barang_id ? 'selected' : '' }}>
                                                    {{ $b->barang_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="error-text form-text text-danger"></small>
                                    </td>
                                    <td>
                                        <input type="number" name="harga[]" class="form-control harga-input" value="{{ $detail->harga }}" readonly>
                                        <small class="error-text form-text text-danger"></small>
                                    </td>
                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="{{ $detail->jumlah }}" required>
                                        <small class="error-text form-text text-danger"></small>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control subtotal-display" value="{{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}" readonly>
                                        <input type="hidden" class="subtotal-input" value="{{ $detail->harga * $detail->jumlah }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total:</th>
                                <th id="total-amount">0</th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Template detail item row  -->
    <template id="detail-row-template">
        <tr class="detail-row">
            <td>
                <select name="barang_id[]" class="form-control barang-select" required>
                    <option value="">- Pilih Barang -</option>
                    @foreach ($barang as $b)
                        <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}">{{ $b->barang_nama }}</option>
                    @endforeach
                </select>
                <small class="error-text form-text text-danger"></small>
            </td>
            <td>
                <input type="number" name="harga[]" class="form-control harga-input" readonly>
                <small class="error-text form-text text-danger"></small>
            </td>
            <td>
                <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="1" required>
                <small class="error-text form-text text-danger"></small>
            </td>
            <td>
                <input type="text" class="form-control subtotal-display" readonly>
                <input type="hidden" class="subtotal-input">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <script>
        $(document).ready(function() {

            // Calculate init total
            calculateTotal();

            // Add new detail row
            $("#btn-add-item").click(function() {
                addDetailRow();
            });

            // Remove detail row
            $(document).on('click', '.btn-remove-item', function() {
                if ($('.detail-row').length > 1) {
                    $(this).closest('tr').remove();
                    calculateTotal();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak dapat dihapus',
                        text: 'Minimal harus ada satu barang'
                    });
                }
            });

            // When barang is selected, set the price
            $(document).on('change', '.barang-select', function() {
                var row = $(this).closest('tr');
                var harga = $(this).find(':selected').data('harga') || 0;
                row.find('.harga-input').val(harga);
                updateRowTotal(row);
            });

            // When quantity changes, update subtotal
            $(document).on('input', '.jumlah-input', function() {
                updateRowTotal($(this).closest('tr'));
            });

            $("#form-edit").validate({
                rules: {
                    pembeli: { required: true, maxlength: 200 },
                    penjualan_tanggal: { required: true },
                    user_id: { required: true },
                    "barang_id[]": { required: true },
                    "jumlah[]": { required: true, min: 1 }
                },
                submitHandler: function(form) {
                    // Check if at least one item exists
                    if ($('.detail-row').length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Minimal harus ada satu barang'
                        });
                        return false;
                    }

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                tablePenjualan.ajax.reload();
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
                        },
                        // error: function (xhr, status, error) {
                        //     console.log('debug error');
                        //     var response = xhr.responseJSON;
                        //     if (response && response.message) {
                        //         Swal.fire({
                        //             icon: 'error',
                        //             title: 'Terjadi Kesalahan',
                        //             text: response.message
                        //         });
                        //     } else {
                        //         Swal.fire({
                        //             icon: 'error',
                        //             title: 'Terjadi Kesalahan',
                        //             text: 'Tidak dapat menyimpan data'
                        //         });
                        //     }
                        // }
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

            // Function to add a new detail row
            function addDetailRow() {
                var template = document.getElementById('detail-row-template');
                var clone = document.importNode(template.content, true);
                $('#detail-items').append(clone);

                // Initialize the new row (set price and subtotal)
                var newRow = $('#detail-items tr:last');
                updateRowTotal(newRow);
            }

            // Function to update a row's subtotal
            function updateRowTotal(row) {
                var harga = parseFloat(row.find('.harga-input').val()) || 0;
                var jumlah = parseInt(row.find('.jumlah-input').val()) || 0;
                var subtotal = harga * jumlah;

                row.find('.subtotal-input').val(subtotal);
                row.find('.subtotal-display').val(formatRupiah(subtotal));

                calculateTotal();
            }

            // Function to calculate the total amount
            function calculateTotal() {
                var total = 0;
                $('.subtotal-input').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });

                $('#total-amount').text(formatRupiah(total));
            }

            // Function to format number as Rupiah
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }

        });

    </script>
@endempty