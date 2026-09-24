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
            <label>ID Artikel</label>
            <input type="number" name="id_menu" class="form-control" value="{{ old('id_menu', $row->article_id ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $row->urutan ?? 0) }}" required>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('pilihan') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
