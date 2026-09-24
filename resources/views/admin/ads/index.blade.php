@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('ads/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead>
                <tr><th>#</th><th>Posisi</th><th>Tipe</th><th>File Tipe</th><th>Gambar</th><th>Link Tujuan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @foreach ($ads as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $r->ads_position }}</td>
                    <td>{{ $r->ads_type }}</td>
                    <td>{{ $r->ads_file_type }}</td>
                    <td>
                        @if ($r->ads_type == 0 && $r->ads_url)
                            <img src="{{ url('uploads/i/'.$r->ads_url) }}" alt="Iklan posisi {{ $r->ads_position }}" style="height:40px;width:auto;">
                        @else
                            <span class="muted">-</span>
                        @endif
                    </td>
                    <td style="max-width:360px;">
                        @if (!empty($r->ads_link) && $r->ads_link !== '#')
                            <a href="{{ $r->ads_link }}" target="_blank" rel="noopener" style="word-break:break-all;">{{ \Illuminate\Support\Str::limit($r->ads_link, 70) }}</a>
                        @else
                            <span class="muted">-</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ site_admin('ads/edit/'.md5($r->ads_id)) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                        <a href="{{ site_admin('ads/delete/'.md5($r->ads_id)) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
