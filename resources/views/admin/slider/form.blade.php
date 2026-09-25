@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('slider/edit/'.$row->id) : site_admin('slider/add') }}">
        @csrf
        <div class="form-group">
            <label for="articleSelect">Artikel</label>
            <select name="article_id" id="articleSelect" class="form-control" style="width:100%" required>
                @if ($row && $articleTitle)
                    <option value="{{ $row->article_id }}" selected>{{ $articleTitle }}</option>
                @endif
            </select>
            <small class="form-text text-muted">Ketik minimal 2 huruf untuk mencari judul artikel yang sudah terbit.</small>
        </div>
        <div class="form-group">
            <label for="urutan">Urutan</label>
            <input type="number" name="urutan" id="urutan" class="form-control" value="{{ old('urutan', $row->urutan ?? '') }}" placeholder="Kosongkan untuk otomatis di akhir">
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('slider') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    $(function () {
        $('#articleSelect').select2({
            placeholder: 'Cari judul artikel…',
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: '{{ site_admin('slider/articles') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data.results }; }
            }
        });
    });
</script>
@endsection
