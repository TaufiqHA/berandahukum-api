@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('label/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Nama</th><th>URI</th><th>Tampil</th><th>Urutan</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($label as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->label_name }}</td>
                <td>{{ $r->label_uri }}</td>
                <td>{{ $r->label_show }}</td>
                <td>{{ $r->urutan }}</td>
                <td>
                    <a href="{{ site_admin('label/edit/'.$r->label_id) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                    <a href="{{ site_admin('label/delete/'.$r->label_id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
