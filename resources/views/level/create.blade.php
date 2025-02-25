@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Level Baru</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('level.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="level_kode">Kode Level</label>
            <input type="text" class="form-control" id="level_kode" name="level_kode" value="{{ old('level_kode') }}" required>
        </div>

        <div class="form-group">
            <label for="level_nama">Nama Level</label>
            <input type="text" class="form-control" id="level_nama" name="level_nama" value="{{ old('level_nama') }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        <a href="{{ route('level.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </form>
</div>
@endsection
