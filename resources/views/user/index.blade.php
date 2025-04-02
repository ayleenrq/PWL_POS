@extends('layouts.template') 
 
@section('content') 
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar User</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/user/import') }}')" class="btn btn-info">Import User</button>
            <a href="{{ url('/user/create') }}" class="btn btn-primary">Tambah Data</a>
            <button onclick="modalAction('{{ url('/user/create_ajax') }}')" class="btn btn-success">Tambah Data (Ajax)</button>
        </div>
    </div>
    <div class="card-body">
      <!-- untuk filter data -->
      <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group form-group-sm row text-sm mb-0">
              <label for="filter_level" class="col-md-1 col-form-label">Filter</label>
              <div class="col-md-3">
                <select name="filter_level" class="form-control form-control-sm filter_level">
                  <option value="">-- Semua --</option>
                  @foreach($level as $i)
                    <option value="{{ $i->level_id }}">{{ $i->level_nama }}</option>
                  @endforeach
                </select>
                <small class="form-text text-muted">Level Pengguna</small>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
        
<table class="table table-bordered table-striped table-hover table-sm" id="table_user">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Level Pengguna</th>
            <th>Aksi</th>
        </tr>
    </thead>
</table> 

  <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data
    backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true">
  </div> 

@endsection 
 
@push('js') 
  <script> 
    function modalAction(url = '') { 
      $('#myModal').load(url, function() { 
        $('#myModal').modal('show'); 
      });
    } 
    
    var dataUser;
    $(document).ready(function() { 
      dataUser = $('#table_user').DataTable({ 
        processing: true,
        serverSide: true,      
        ajax: { 
            "url": "{{ url('user/list') }}", 
            "dataType": "json", 
            "type": "POST",
            "data": function(d) {
                d.filter_level = $('.filter_level').val();
            }
        }, 
        columns: [ 
            { 
              // nomor urut dari laravel datatable addIndexColumn() 
              data: "DT_RowIndex",             
              className: "text-center", 
              width: "10%", 
              orderable: false, 
              searchable: false     
            },{ 
              data: "username",                
              className: "", 
              width: "20%", 
              orderable: true,      
              searchable: true     
            },{ 
              data: "nama",                
              className: "", 
              width: "20%", 
              orderable: true,     
              searchable: true      
            },{ 
              // mengambil data level hasil dari ORM berelasi 
              data: "level.level_nama",                
              className: "", 
              width: "20%", 
              orderable: true,     
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
      
      $('#table-user_filter input').unbind().bind().on('keyup', function(e){
        if(e.keyCode == 13){ // enter key
          dataUser.search(this.value).draw();
        }
      })

      $('.filter_level').change(function(){
        dataUser.draw();
      });
    }); 
  </script> 
@endpush
