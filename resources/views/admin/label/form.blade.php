@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('label/edit/'.$row->label_id) : site_admin('label/add') }}">
        @csrf
        <div class="form-group">
            <label>Nama Label</label>
            <input type="text" name="labelName" class="form-control" value="{{ old('labelName', $row->label_name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $row->urutan ?? 0) }}">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="labelShow" value="yes" @checked(($row->label_show ?? 'yes') === 'yes')> Tampilkan</label>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('label') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
