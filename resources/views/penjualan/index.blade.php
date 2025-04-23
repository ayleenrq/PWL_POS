@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary"> 
      <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Penjualan</a>
            <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Penjualan</a>
            <button onclick="modalAction('{{ url('/penjualan/create_ajax') }}')" class="btn btn-success">Buat Penjualan</button>
        </div>
      </div> 
      <div class="card-body">
        <!-- untuk filter kasir/user -->
        <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-sm row text-sm mb-0">
                        <label for="filter_date" class="col-md-1 col-form-label">Filter</label>
                        <div class="col-md-3">
                            <select name="filter_user" class="form-control form-control-sm filter_user">
                                <option value="">-- Semua --</option>
                                @foreach($user as $i)
                                    <option value="{{ $i->user_id }}">{{ $i->nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Kasir</small>
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
        <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Penjualan</th>
                    <th>Tanggal/Waktu</th>
                    <th>Kasir</th>
                    <th>Pembeli</th>
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
    function modalAction(url = ''){
        $('#myModal').load(url, function(){
            $('#myModal').modal('show');
        });
    }

    var tablePenjualan;
    $(document).ready(function(){
    tablePenjualan = $('#table_penjualan').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            "url": "{{ url('/penjualan/list') }}",
            "dataType": "json",
            "type": "POST",
            "data": function (d) {
                d.filter_user = $('.filter_user').val();
            }
        },
        columns: [{
            data: "penjualan_id",
            className: "text-center",
            orderable: false,
            searchable: false
        },{
            data: "penjualan_kode",
            className: "",
            orderable: true,
            searchable: false
        },{
            data: "penjualan_tanggal",
            className: "",
            orderable: true,
            searchable: false
        },{
            data: "user.nama",
            className: "",
            orderable: true,
            searchable: true
        },{
            data: "pembeli",
            className: "",
            orderable: true,
            searchable: true
        },{
            data: "aksi",
            className: "text-center",
            orderable: false,
            searchable: false
        }]
    });

    // Event listener for filter dropdown
    $('.filter_user').change(function() {
        tablePenjualan.ajax.reload(); // Reload DataTable with the new filter
    });
});
</script>
@endpush
