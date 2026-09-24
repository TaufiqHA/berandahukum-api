@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('menu/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Tipe</th><th>ID Menu</th><th>URI</th><th>Urutan</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($datamenu as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->tipe }}</td>
                <td>{{ $r->id_menu }}</td>
                <td>{{ $r->uri_menu }}</td>
                <td>{{ $r->urutan }}</td>
                <td>
                    <a href="{{ site_admin('menu/edit/'.$r->id) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                    <a href="{{ site_admin('menu/delete/'.$r->id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
