<form action="{{ url('/penjualan/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode Penjualan</label>
                    <input type="text" name="penjualan_kode" class="form-control" required>
                    <small id="error-penjualan_kode" class="error-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Pembeli</label>
                    <input type="text" name="pembeli" class="form-control" required>
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
                        <tbody id="detail-items"></tbody>
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

<template id="detail-row-template">
    <tr class="detail-row">
        <td>
            <select name="barang_id[]" class="form-control barang-select" required>
                <option value="">- Pilih Barang -</option>
                @foreach ($barang as $b)
                    <option value="{{ $b->barang_id }}" data-harga="{{ $b->harga_jual }}">{{ $b->barang_nama }}</option>
                @endforeach
            </select>
            <small class="error-text text-danger"></small>
        </td>
        <td>
            <input type="number" name="harga[]" class="form-control harga-input" readonly>
            <small class="error-text text-danger"></small>
        </td>
        <td>
            <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" value="1" required>
            <small class="error-text text-danger"></small>
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
    addDetailRow();

    $(document).on('click', '#btn-add-item', function() {
        addDetailRow();
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

    $("#form-tambah").submit(function(e) {
        e.preventDefault();

        if ($('.detail-row').length === 0) {
            Swal.fire('Minimal harus ada satu barang');
            return;
        }

        $.ajax({
            url: this.action,
            type: this.method,
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    $('#modal-master').modal('hide');
                    Swal.fire('Sukses', response.message, 'success').then(function() {
                        location.reload();
                    });
                    tablePenjualan.ajax.reload();
                } else {
                    $('.error-text').text('');
                    $.each(response.msgField, function(key, val) {
                        $('#error-' + key).text(val[0]);
                    });
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });

    function addDetailRow() {
        let template = document.getElementById('detail-row-template');
        let clone = document.importNode(template.content, true);
        $('#detail-items').append(clone);
    }

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
