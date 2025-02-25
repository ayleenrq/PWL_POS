@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Level</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('level.create') }}" class="btn btn-primary mb-3">Tambah Level Baru</a>

    <table class="table">
        <thead>
            <tr>
                <th>Kode Level</th>
                <th>Nama Level</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($levels as $level)
            <tr>
                <td>{{ $level->level_kode }}</td>
                <td>{{ $level->level_nama }}</td>
                <td>{{ $level->created_at }}</td>
                <td>
                    <a href="{{ route('level.edit', $level->level_kode) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('level.destroy', $level->level_kode) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
