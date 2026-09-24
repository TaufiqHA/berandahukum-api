@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <div class="table-responsive">
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Artikel</th><th>Nama</th><th>Komentar</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @php
            $rows = \App\Models\Comment::orderBy('comment_status')->orderByDesc('comment_date')->get();
        @endphp
        @foreach ($rows as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \App\Models\Article::find($r->article_id)->article_title ?? '(terhapus)' }}</td>
                <td>{{ $r->comment_name }}</td>
                <td>{!! nl2br(e($r->comment_fill)) !!}@if ($r->comment_reply)<br><b>Jawaban:</b><br>{!! nl2br(e($r->comment_reply)) !!}@endif</td>
                <td>{{ $r->comment_date }}</td>
                <td>{{ $r->comment_status == 1 ? 'Tampil' : 'Tidak' }}</td>
                <td>
                    <a href="{{ site_admin('komentar/reply/'.$r->comment_id) }}" class="btn btn-outline-success btn-sm">Balas</a>
                    @if ($r->comment_status == 1)
                        <a href="{{ site_admin('komentar/unpublish/'.$r->comment_id) }}" class="btn btn-outline-primary btn-sm">Sembunyikan</a>
                    @else
                        <a href="{{ site_admin('komentar/publish/'.$r->comment_id) }}" class="btn btn-outline-primary btn-sm">Tampilkan</a>
                    @endif
                    <a href="{{ site_admin('komentar/delete/'.$r->comment_id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
