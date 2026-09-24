@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('banner/edit/'.md5($row->id_banner)) : site_admin('banner/add') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Nama Banner</label>
            <input type="text" name="bannerName" class="form-control" value="{{ old('bannerName', $row->nama_banner ?? '') }}">
        </div>
        <div class="form-group">
            <label>Link URL</label>
            <input type="text" name="bannerLink" class="form-control" value="{{ old('bannerLink', $row->link_url ?? '') }}">
        </div>
        <div class="form-group">
            <label>Gambar</label>
            <input type="file" name="file_banner" class="form-control" accept="image/*">
            @if (!empty($row->file_banner))<small>File saat ini: {{ $row->file_banner }}</small>@endif
        </div>
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $row->urutan ?? 0) }}" required>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="bannerStatus" value="yes" @checked(($row->status ?? 'yes') === 'yes')> Tampilkan</label>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('banner') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
