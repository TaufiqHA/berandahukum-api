@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('ads/edit/'.md5($row->ads_id)) : site_admin('ads/add') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Posisi (1-35)</label>
            <input type="number" name="adsPosition" class="form-control" value="{{ old('adsPosition', $row->ads_position ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Tipe Iklan</label>
            <select name="adsType" class="form-control">
                <option value="0" @selected(($row->ads_type ?? '0') == '0')>Gambar Upload</option>
                <option value="1" @selected(($row->ads_type ?? '0') == '1')>URL / Embed</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tipe File</label>
            <select name="adsFileType" class="form-control">
                <option value="0" @selected(($row->ads_file_type ?? '0') == '0')>Gambar</option>
                <option value="1" @selected(($row->ads_file_type ?? '0') == '1')>Iframe</option>
                <option value="2" @selected(($row->ads_file_type ?? '0') == '2')>HTML / Kode</option>
            </select>
        </div>
        <div class="form-group">
            <label>Link</label>
            <input type="text" name="ads_link" class="form-control" value="{{ old('ads_link', $row->ads_link ?? '#') }}">
        </div>
        <div class="form-group">
            <label>Upload Gambar</label>
            @include('partials.file_field', ['name' => 'adsFile', 'accept' => 'image/*', 'current' => $row->ads_url ?? null])
        </div>
        <div class="form-group">
            <label>URL / Kode Embed</label>
            <input type="text" name="adsUrl" class="form-control" value="{{ old('adsUrl', $row->ads_url ?? '') }}">
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('ads') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
