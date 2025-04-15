<!-- resources/views/profile/change-photo.blade.php -->
@extends('layouts.template')

@section('content')
<div class="container" style="display: flex; justify-content: center;">
    <div class="col-md-6">
        <div class="card shadow" style="border-radius: 15px;">
            <div class="card-header">
                <h3 class="card-title">Change Profile Photo</h3>
            </div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="card-body">
                <div class="text-center">
                    <img src="{{ asset('storage/profile/' . (Auth::user()->profile_picture ?? 'user.png')) }}"
                        class="img-circle" 
                        alt="User Image" 
                        style="width: 127px; height: 127px;">
                </div>
                <form action="{{ route('update-photo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="profile_picture">New Profile Photo</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" required style="border-radius: 10px; display: block; margin: 0px">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Photo</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow" style="border-radius: 15px;">
            <div class="card-header">
                <h3 class="card-title">Profile Information</h3>
            </div>
            <div class="modal-body">
                <div class="form-group"> 
                    <label>Level Pengguna</label> 
                    <div class="form-control" style="border-radius: 15px;" id="level_id" disabled> 
                        {{ Auth::user()->level->level_nama }}
                    </div> 
                </div> 
                <div class="form-group"> 
                    <label>Username</label> 
                    <div class="form-control" style="border-radius: 15px;" id="username" disabled> 
                        {{ Auth::user()->username }}
                    </div> 
                </div> 
                <div class="form-group"> 
                    <label>Nama</label> 
                    <div class="form-control" style="border-radius: 15px;" id="nama" disabled> 
                        {{ Auth::user()->nama }}
                    </div> 
                </div> 
    
            </div>
        </div>
    </div>
</div>
@endsection
