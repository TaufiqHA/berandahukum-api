@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('sub-category/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Kategori</th><th>Sub Kategori</th><th>URI</th><th>Tampil</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($subCategory as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $categories[$r->category_id]->category_name ?? '-' }}</td>
                <td>{{ $r->sub_category_name }}</td>
                <td>{{ $r->sub_category_uri }}</td>
                <td>{{ $r->sub_category_show }}</td>
                <td>
                    <a href="{{ site_admin('sub-category/edit/'.$r->sub_category_id) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                    <a href="{{ site_admin('sub-category/delete/'.$r->sub_category_id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
