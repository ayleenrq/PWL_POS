@extends('layouts.template')

@section('content')
    <div class="row">
        @foreach ($rekap as $barang)
            <div class="col-4 col-sm-4 col-md-2">
            <div class="info-box mb-3" style="border-radius: 10px;">
                <div class="info-box-content">
                <span class="info-box-text" style="color:rgb(63, 63, 63)">{{ $barang->barang_nama }}</span>
                <span class="info-box-number">{{ $barang->stok_aktual }}</span>
                </div>
            </div>
            </div>
        @endforeach
    </div>
    <div class="card card-outline card-primary"> 
      <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">Import Stok</button>
            <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Stok</a>
            <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Stok</a>
            <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-success">Tambah Stok Barang</button>
        </div>
      </div> 
      <div class="card-body">
        <!-- untuk filter barang -->
        <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="filter_date" class="col-md-1 col-form-label">Filter</label>
                        <div class="col-md-3">
                            <select name="filter_barang" class="form-control form-control-sm filter_barang">
                                <option value="">-- Semua --</option>
                                @foreach($rekap as $i)
                                    <option value="{{ $i->barang_id }}">{{ $i->barang_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Kategori Barang</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_stok">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal/Waktu</th>
                    <th>Barang</th>
                    <th>Jenis Stok</th>
                    <th>Supplier</th>
                    <th>User</th>
                    <th>Stok</th>
                    <!-- <th>Aksi</th> -->
                </tr>
            </thead>
        </table>
      </div>
    </div>
    </div>

  <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data
    backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true">
  </div>
@endsection

@push('css') 
@endpush 

@push('js') 
<script> 
    function modalAction(url = '') { 
      $('#myModal').load(url, function() { 
        $('#myModal').modal('show'); 
      }); 
    } 

    var dataStok;
    $(document).ready(function() { 
    dataStok = $('#table_stok').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('/stok/list') }}",
            type: 'POST',
            data: function(d) {
                d.filter_barang = $('.filter_barang').val(); // Get the selected filter value
            }
        },
        columns: [
            {data: 'stok_id'},
            {data: 'tanggal'},
            {data: 'barang.barang_nama'},
            {data: 'jenis_stok'},
            {data: 'supplier.supplier_nama', defaultContent: '-'},
            {data: 'user.nama'},
            {data: 'stok_jumlah'},
            // {data: 'aksi'}
        ]
    });

    // Event listener for filter dropdown
    $('.filter_barang').change(function() {
        dataStok.ajax.reload(); // Reload DataTable with the new filter
    });
}); 
</script>
@endpush
