@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <div class="card mb-3">
        <div class="card-body">
            <p><b>{{ $comment->comment_name }}</b></p>
            <p>{!! nl2br(e($comment->comment_fill)) !!}</p>
        </div>
    </div>
    <form method="post" action="{{ site_admin('komentar/reply/'.$comment->comment_id) }}">
        @csrf
        <div class="form-group">
            <label>Jawaban</label>
            <textarea name="comment_reply" class="form-control" rows="5" required>{{ old('comment_reply', $comment->comment_reply) }}</textarea>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('komentar') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
