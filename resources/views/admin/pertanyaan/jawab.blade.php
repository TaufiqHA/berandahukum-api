@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <div class="card mb-3">
        <div class="card-body">
            <p><b>{{ $pertanyaan->pertanyaan_nama }}</b> ({{ $pertanyaan->pertanyaan_email }})</p>
            <p>{{ $pertanyaan->pertanyaan }}</p>
        </div>
    </div>
    <form method="post" action="{{ site_admin('pertanyaan/jawab/'.$pertanyaan->pertanyaan_id) }}">
        @csrf
        <div class="form-group">
            <label>Jawaban</label>
            <textarea name="pertanyaan_jawab" class="form-control" rows="6" required>{{ old('pertanyaan_jawab', $pertanyaan->pertanyaan_jawaban) }}</textarea>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('pertanyaan') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
