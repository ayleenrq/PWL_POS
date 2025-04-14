@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $page->title }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle"
                                            src="{{ Auth::user()->profile_photo_url ?? 'user.png' }}"
                                            alt="User profile picture">
                                    </div>

                                    <h3 class="profile-username text-center">{{ Auth::user()->username }}</h3>

                                    <p class="text-muted text-center">{{ Auth::user()->role }}</p>

                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>Username</b> <a class="float-right">{{ Auth::user()->username }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Email</b> <a class="float-right">{{ Auth::user()->email }}</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Role</b> <a class="float-right">{{ Auth::user()->role }}</a>
                                        </li>
                                    </ul>

                                    <a href="{{ route('change-photo') }}" class="btn btn-primary btn-block">
                                        <b>Change Photo</b>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#settings" data-toggle="tab">Settings</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="settings">
                                            <form class="form-horizontal">
                                                <div class="form-group row">
                                                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="username" 
                                                            value="{{ Auth::user()->username }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" class="form-control" id="email" 
                                                            value="{{ Auth::user()->email }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="password" class="col-sm-2 col-form-label">Change Password</label>
                                                    <div class="col-sm-10">
                                                        <input type="password" class="form-control" id="password" placeholder="Enter new password">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password</label>
                                                    <div class="col-sm-10">
                                                        <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm new password">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="offset-sm-2 col-sm-10">
                                                        <button type="button" class="btn btn-danger" id="update-profile">Update Profile</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#update-profile').click(function() {
        const password = $('#password').val();
        const password_confirmation = $('#password_confirmation').val();
        
        if (password !== password_confirmation) {
            alert('Password confirmation does not match');
            return;
        }

        $.ajax({
            url: '{{ route("update-profile") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                password: password
            },
            success: function(response) {
                alert('Profile updated successfully');
            },
            error: function(xhr) {
                alert('Error updating profile');
            }
        });
    });
});
</script>
@endsection