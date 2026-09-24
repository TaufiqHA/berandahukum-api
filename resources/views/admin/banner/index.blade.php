@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('banner/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Nama</th><th>Link</th><th>Gambar</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($banner as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->nama_banner }}</td>
                <td>{{ $r->link_url }}</td>
                <td>{{ $r->file_banner }}</td>
                <td>{{ $r->urutan }}</td>
                <td>{{ $r->status }}</td>
                <td>
                    <a href="{{ site_admin('banner/edit/'.md5($r->id_banner)) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                    <a href="{{ site_admin('banner/delete/'.md5($r->id_banner)) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
