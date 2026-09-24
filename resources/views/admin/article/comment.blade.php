@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }} — {{ $article->article_title }}</h4>
    <div class="table-responsive">
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Nama</th><th>Komentar</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($comments as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->comment_name }}</td>
                <td>{!! nl2br(e($r->comment_fill)) !!}@if ($r->comment_reply)<br><b>Jawaban:</b><br>{!! nl2br(e($r->comment_reply)) !!}@endif</td>
                <td>{{ $r->comment_date }}</td>
                <td>{{ $r->comment_status == 1 ? 'Tampil' : 'Tidak' }}</td>
                <td>
                    @if ($r->comment_status == 1)
                        <a href="{{ site_admin('article/unpublish-komentar/'.$r->comment_id) }}" class="btn btn-outline-primary btn-sm">Sembunyikan</a>
                    @else
                        <a href="{{ site_admin('article/publish-komentar/'.$r->comment_id) }}" class="btn btn-outline-primary btn-sm">Tampilkan</a>
                    @endif
                    <a href="{{ site_admin('article/delete-komentar/'.$r->comment_id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <a href="{{ site_admin('article') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
