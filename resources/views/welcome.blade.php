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
  <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>150</h3>

                <p>New Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>53<sup style="font-size: 20px">%</sup></h3>

                <p>Bounce Rate</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>44</h3>

                <p>User Registrations</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>

                <p>Unique Visitors</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
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