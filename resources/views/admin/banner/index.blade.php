@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="admin-page-head">
        <h1>{{ $title }}</h1>
        <a href="{{ site_admin('banner/add') }}" class="btn btn-primary">Tambah</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped">
            <thead><tr><th>#</th><th>Nama</th><th>Link</th><th>Gambar</th><th>Urutan</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @foreach ($banner as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $r->nama_banner }}</td>
                    <td>{{ $r->link_url }}</td>
                    <td>{{ $r->file_banner }}</td>
                    <td>{{ $r->urutan }}</td>
                    <td>
                        @if ($r->status == 1)
                            <span class="status-badge is-publish">Aktif</span>
                        @else
                            <span class="status-badge is-muted">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="admin-actions">
                            <a href="{{ site_admin('banner/edit/'.md5($r->id_banner)) }}" class="btn-icon" title="Ubah"><i class="fa fa-pencil"></i></a>
                            <a href="{{ site_admin('banner/delete/'.md5($r->id_banner)) }}" onclick="return confirm('Yakin hapus?')" class="btn-icon is-danger" title="Hapus"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
