@extends('layouts.template')

@section('content')
  <div class="row">
  @foreach ($stoks as $stok)
    <div class="col-4 col-sm-4 col-md-2">
      <div class="info-box mb-3" style="border-radius: 10px;">
        <div class="info-box-content">
          <span class="info-box-text" style="color:rgb(63, 63, 63)">{{ $stok->barang->barang_nama }}</span>
          <span class="info-box-number">{{ $stok->stok_jumlah }}</span>
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
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_riwayat_stok">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal/Waktu</th>
                    <th>Barang</th>
                    <th>Supplier</th>
                    <th>User</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
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
      dataStok = $('#table_riwayat_stok').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "{{ url('/stok/riwayat') }}",
          type: 'POST',
          data: function(d) {
            d._token = '{{ csrf_token() }}';
          }
        },
        columns: [
          {data: 'riwayat_stok_id'},
          {data: 'tanggal'},
          {data: 'barang.barang_nama'},
          {data: 'supplier.supplier_nama'},
          {data: 'user.nama'},
          {data: 'stok_jumlah'},
          {data: 'aksi'}
        ]
      });
    }); 
</script>
@endpush
