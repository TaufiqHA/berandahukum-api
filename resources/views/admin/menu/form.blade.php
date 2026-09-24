@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('menu/edit/'.$row->id) : site_admin('menu/add') }}">
        @csrf
        <div class="form-group">
            <label>Tipe</label>
            <select name="tipe" id="tipe" class="form-control" required>
                @foreach (['artikel', 'label', 'kategori', 'subkategori'] as $t)
                    <option value="{{ $t }}" @selected(($row->tipe ?? '') === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>ID Menu (ID sumber sesuai tipe)</label>
            <input type="number" name="id_menu" class="form-control" value="{{ old('id_menu', $row->id_menu ?? '') }}" required>
            <small class="text-muted">Contoh: untuk tipe artikel, isi article_id; untuk kategori, category_id.</small>
        </div>
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $row->urutan ?? 0) }}" required>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('menu') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
