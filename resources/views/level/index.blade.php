@extends('layouts.template')

@section('content')
  <div class="card card-outline card-primary"> 
      <div class="card-header">
        <h3 class="card-title">Daftar Level</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/level/import') }}')" class="btn btn-info">Import Level</button>
            <a href="{{ url('/level/create') }}" class="btn btn-primary">Tambah Data</a>
            <button onclick="modalAction('{{ url('/level/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button>
        </div>
      </div>
      <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_level">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode Level</th>
                    <th>Level Nama</th>
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

    var dataLevel;
    $(document).ready(function() { 
      dataLevel = $('#table_level').DataTable({ 
          serverSide: true,      
          processing: true,
          ajax: { 
              "url": "{{ url('level/list') }}", 
              "dataType": "json", 
              "type": "POST"
          }, 
          columns: [ 
            { 
              // nomor urut dari laravel datatable addIndexColumn() 
              data: "DT_RowIndex",             
              className: "text-center", 
              width: "5%", 
              orderable: false, 
              searchable: false     
            },{ 
              data: "level_kode",                
              className: "", 
              width: "20%", 
              orderable: true,     
              searchable: true      
            },{ 
              // mengambil data level hasil dari ORM berelasi 
              data: "level_nama",                    
              className: "", 
              width: "20%", 
              orderable: false,     
              searchable: false     
            },{ 
              data: "aksi",                
              className: "", 
              width: "20%", 
              orderable: false,     
              searchable: false     
            } 
          ] 
      }); 
      
    }); 
</script>
@endpush
