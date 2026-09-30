@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('pilihan/edit/'.$row->id) : site_admin('pilihan/add') }}">
        @csrf
        <div class="form-group">
            <label>Posisi</label>
            <select name="posisi" class="form-control" required>
                @foreach (['atas', 'bawah', 'top'] as $p)
                    <option value="{{ $p }}" @selected(($row->posisi ?? '') === $p)>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="articleSelect">Artikel</label>
            <select name="id_menu" id="articleSelect" class="form-control" style="width:100%" required>
                @if ($row && $articleTitle)
                    <option value="{{ $row->article_id }}" selected>{{ $articleTitle }}</option>
                @endif
            </select>
            <small class="form-text text-muted">Ketik minimal 2 huruf untuk mencari judul artikel yang sudah terbit.</small>
        </div>
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $row->urutan ?? 0) }}" required>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('pilihan') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    $(function () {
        $('#articleSelect').select2({
            placeholder: 'Cari judul artikel…',
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: '{{ site_admin('pilihan/articles') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; }
            }
        });
    });
</script>
@endsection
