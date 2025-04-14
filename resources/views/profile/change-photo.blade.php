<!-- resources/views/profile/change-photo.blade.php -->
@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
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
                            style="width: 150px; height: 150px;">
                    </div>
                    <form action="{{ route('update-photo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="profile_picture">New Profile Photo</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Photo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection