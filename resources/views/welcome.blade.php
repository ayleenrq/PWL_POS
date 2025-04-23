@extends('layouts.template')

@section('content')
  <div class="row">
    <div class="col-4 col-sm-4 col-md-3">
      <div class="card bg-info" style="border-radius: 15px;">
        <div class="card-header">
          <h3 class="card-title">Total Penjualan</h3>
        </div>
        <div class="card-body text-right">
          <span class="info-box-number" style="font-size: 28px; text-align: right">{{ $total }}</span>
        </div>
      </div>
    </div>
    <div class="col-4 col-sm-4 col-md-3">
      <div class="card bg-info" style="border-radius: 15px;">
        <div class="card-header">
          <h3 class="card-title">Total Stok</h3>
        </div>
        <div class="card-body text-right">
          <span class="info-box-number" style="font-size: 28px; text-align: right">{{ $totalStok }}</span>
        </div>
      </div>
    </div>
    <div class="col-4 col-sm-4 col-md-3">
      <div class="card bg-info" style="border-radius: 15px;">
        <div class="card-header">
          <h3 class="card-title">Total Pengguna</h3>
        </div>
        <div class="card-body text-right">
          <span class="info-box-number" style="font-size: 28px; text-align: right">{{ $totalUser }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="card card-outline card-primary"> 
      <div class="card-header">
        <h3 class="card-title"></h3>
        <div class="card-tools">
            
       </div>
      </div> 
      <div class="card-body">
        
      </div>
  </div>
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
</script>
@endpush