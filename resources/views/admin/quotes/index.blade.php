@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('quotes/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <div class="table-responsive">
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Gambar</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($quotes as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->quote_image }}</td>
                <td>{{ $r->urutan }}</td>
                <td>{{ $r->quote_status == '1' ? 'Aktif' : 'Tidak' }}</td>
                <td>
                    <a href="{{ site_admin('quotes/edit/'.md5($r->quote_id)) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                    <a href="{{ site_admin('quotes/delete/'.md5($r->quote_id)) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
