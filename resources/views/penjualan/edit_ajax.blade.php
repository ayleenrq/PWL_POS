<form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode Penjualan</label>
                    <input type="text" name="penjualan_kode" class="form-control" value="{{ $penjualan->penjualan_kode }}" readonly>
                </div>
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" class="form-control" value="{{ $penjualan->pembeli }}" required>
                    <small id="error-pembeli" class="error-text text-danger"></small>
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
                    <table class="table table-bordered" id="detail-table">
                        <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Harga (Rp)</th>
                            <th>Jumlah</th>
                            <th>Subtotal (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody id="detail-items">
                        @foreach ($penjualan->penjualan_detail as $item)
                            <tr class="detail-row">
                                <td>
                                    <select name="barang_id[]" class="form-control barang-select" required>
                                        <option value="">- Pilih Barang -</option>
                                        @foreach ($barang as $b)
                                            <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}"
                                                {{ $item->barang_id == $b->barang_id ? 'selected' : '' }}>
                                                {{ $b->barang_nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="harga[]" class="form-control harga-input" readonly value="{{ $item->harga }}">
                                </td>
                                <td>
                                    <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="{{ $item->jumlah }}" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control subtotal-display" value="{{ number_format($item->total, 0, ',', '.') }}" readonly>
                                    <input type="hidden" class="subtotal-input" value="{{ $item->total }}">
                                </td>
                                <td>
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
                            <th id="total-amount">{{ number_format($penjualan->total, 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</form>

<template id="detail-row-template">
    <tr class="detail-row">
        <td>
            <select name="barang_id[]" class="form-control barang-select" required>
                <option value="">- Pilih Barang -</option>
                @foreach ($barang as $b)
                    <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}">
                        {{ $b->barang_nama }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="harga[]" class="form-control harga-input" readonly>
        </td>
        <td>
            <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="1" required>
        </td>
        <td>
            <input type="text" class="form-control subtotal-display" readonly>
            <input type="hidden" class="subtotal-input">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm btn-remove-item">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
$(document).ready(function() {
    calculateTotal();

    $(document).on('click', '#btn-add-item', function() {
        let template = document.getElementById('detail-row-template');
        let clone = document.importNode(template.content, true);
        $('#detail-items').append(clone);
    });

    $(document).on('click', '.btn-remove-item', function() {
        if ($('.detail-row').length > 1) {
            $(this).closest('tr').remove();
            calculateTotal();
        } else {
            Swal.fire('Minimal harus ada satu barang');
        }
    });

    $(document).on('change', '.barang-select', function() {
        let row = $(this).closest('tr');
        let harga = $(this).find(':selected').data('harga') || 0;
        row.find('.harga-input').val(harga);
        updateRowTotal(row);
    });

    $(document).on('input', '.jumlah-input', function() {
        updateRowTotal($(this).closest('tr'));
    });

    $("#form-edit").submit(function(e) {
        e.preventDefault();

        if ($('.detail-row').length === 0) {
            Swal.fire('Minimal harus ada satu barang');
            return;
        }

        $.ajax({
            url: this.action,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    $('#modal-master').modal('hide');
                    Swal.fire('Berhasil', response.message, 'success').then(function() {
                        location.reload();
                    });
                } else {
                    $('.error-text').text('');
                    $.each(response.msgField, function(key, val) {
                        $('#error-' + key).text(val[0]);
                    });
                    Swal.fire('Gagal', response.message, 'error');
                }
            }
        });
    });

    function updateRowTotal(row) {
        let harga = parseFloat(row.find('.harga-input').val()) || 0;
        let jumlah = parseInt(row.find('.jumlah-input').val()) || 0;
        let subtotal = harga * jumlah;
        row.find('.subtotal-input').val(subtotal);
        row.find('.subtotal-display').val(formatRupiah(subtotal));
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        $('.subtotal-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total-amount').text(formatRupiah(total));
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
});
</script>
