@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('user/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <div class="table-responsive">
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Level</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($user as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->user_name }}</td>
                <td>{{ $r->user_email }}</td>
                <td>{{ $r->user_level }}</td>
                <td>
                    <a href="{{ site_admin('user/edit/'.$r->user_id) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                    <a href="{{ site_admin('user/delete/'.$r->user_id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
