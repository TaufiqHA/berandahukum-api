@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="admin-page-head">
        <h1>{{ $title }}</h1>
        <a href="{{ site_admin('article/add') }}" class="btn btn-primary">Tambah Artikel</a>
    </div>

    <form method="get" class="admin-toolbar">
        <div class="admin-search">
            <i class="fa fa-search"></i>
            <input type="text" name="q" class="form-control" placeholder="Cari judul artikel" value="{{ request('q') }}">
        </div>
        <button class="btn btn-outline-secondary">Cari</button>
        @if (request('q'))
            <a href="{{ url()->current() }}" class="admin-toolbar__reset">Reset</a>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead><tr><th>#</th><th>Judul</th><th>Label</th><th>Status</th><th>Tanggal</th><th class="text-right">Aksi</th></tr></thead>
            <tbody>
            @foreach ($articles as $r)
                <tr>
                    <td>{{ $loop->iteration + ($articles->currentPage() - 1) * $articles->perPage() }}</td>
                    <td>{{ $r->article_title }}</td>
                    <td>{{ $labels[$r->label_id] ?? '-' }}</td>
                    <td>
                        @if ($r->article_status == 1)
                            <span class="status-badge is-publish">Publish</span>
                        @elseif ($r->article_status == 2)
                            <span class="status-badge is-draft">Draft</span>
                        @else
                            <span class="status-badge is-muted">Nonaktif</span>
                        @endif
                    </td>
                    <td>{{ $r->article_date }}</td>
                    <td class="text-right">
                        <div class="admin-actions">
                            <a href="{{ site_admin('article/edit/'.$r->article_id) }}" class="btn-icon" title="Ubah"><i class="fa fa-pencil"></i></a>
                            <a href="{{ site_admin('article/preview/'.$r->article_uri) }}" target="_blank" class="btn-icon" title="Preview"><i class="fa fa-external-link"></i></a>
                            <a href="{{ site_admin('article/comment/'.$r->article_id) }}" class="btn-icon" title="Komentar"><i class="fa fa-comments-o"></i></a>
                            <a href="{{ site_admin('article/referensi/'.$r->article_id) }}" class="btn-icon" title="Referensi"><i class="fa fa-book"></i></a>
                            @if ($r->article_status == 1)
                                <a href="{{ site_admin('article/draft/'.$r->article_id) }}" class="btn-icon" title="Jadikan Draft"><i class="fa fa-eye-slash"></i></a>
                            @else
                                <a href="{{ site_admin('article/publish/'.$r->article_id) }}" class="btn-icon" title="Publish"><i class="fa fa-check"></i></a>
                            @endif
                            <a href="{{ site_admin('article/delete/'.$r->article_id) }}" onclick="return confirm('Yakin hapus?')" class="btn-icon is-danger" title="Hapus"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $articles->links() }}
</div>
@endsection
