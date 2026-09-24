@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('article/add') }}" class="btn btn-primary btn-sm">Tambah Artikel</a>
    </div>
    <form method="get" class="form-inline mb-3">
        <input type="text" name="q" class="form-control mr-2" placeholder="Cari judul" value="{{ request('q') }}">
        <button class="btn btn-outline-secondary">Cari</button>
    </form>
    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead><tr><th>#</th><th>Judul</th><th>Label</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach ($articles as $r)
                <tr>
                    <td>{{ $loop->iteration + ($articles->currentPage() - 1) * $articles->perPage() }}</td>
                    <td>{{ $r->article_title }}</td>
                    <td>{{ $labels[$r->label_id] ?? '-' }}</td>
                    <td>{{ $r->article_status == 1 ? 'Publish' : ($r->article_status == 2 ? 'Draft' : 'Tidak aktif') }}</td>
                    <td>{{ $r->article_date }}</td>
                    <td style="white-space:nowrap;">
                        <a href="{{ site_admin('article/edit/'.$r->article_id) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                        <a href="{{ site_admin('article/preview/'.$r->article_uri) }}" target="_blank" class="btn btn-outline-primary btn-sm">Preview</a>
                        <a href="{{ site_admin('article/comment/'.$r->article_id) }}" class="btn btn-outline-info btn-sm">Komentar</a>
                        <a href="{{ site_admin('article/referensi/'.$r->article_id) }}" class="btn btn-outline-warning btn-sm">Referensi</a>
                        @if ($r->article_status == 1)
                            <a href="{{ site_admin('article/draft/'.$r->article_id) }}" class="btn btn-outline-secondary btn-sm">Draft</a>
                        @else
                            <a href="{{ site_admin('article/publish/'.$r->article_id) }}" class="btn btn-outline-secondary btn-sm">Publish</a>
                        @endif
                        <a href="{{ site_admin('article/delete/'.$r->article_id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $articles->links() }}
</div>
@endsection
