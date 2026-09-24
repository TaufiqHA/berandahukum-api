@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ site_admin('popup') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Status</label>
            <select name="ads_status" class="form-control">
                <option value="on" @selected(($pop->ads_status ?? 'off') === 'on')>On</option>
                <option value="off" @selected(($pop->ads_status ?? 'off') === 'off')>Off</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tipe Iklan</label>
            <select name="adsType" class="form-control">
                <option value="0" @selected(($pop->ads_type ?? '0') == '0')>Gambar Upload</option>
                <option value="1" @selected(($pop->ads_type ?? '0') == '1')>Embed / Kode</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tipe File</label>
            <select name="adsFileType" class="form-control">
                <option value="0" @selected(($pop->ads_file_type ?? '0') == '0')>Gambar</option>
                <option value="1" @selected(($pop->ads_file_type ?? '0') == '1')>Iframe</option>
                <option value="2" @selected(($pop->ads_file_type ?? '0') == '2')>HTML</option>
            </select>
        </div>
        <div class="form-group">
            <label>Link</label>
            <input type="text" name="ads_link" class="form-control" value="{{ $pop->ads_link ?? '#' }}">
        </div>
        <div class="form-group">
            <label>Upload Gambar</label>
            <input type="file" name="adsFile" class="form-control">
            @if (!empty($pop->ads_url))<small>File saat ini: {{ $pop->ads_url }}</small>@endif
        </div>
        <div class="form-group">
            <label>URL / Konten Embed</label>
            <input type="text" name="adsUrl" class="form-control" value="{{ $pop->ads_url ?? '' }}">
        </div>
        <div class="form-group">
            <label>Konten HTML</label>
            <textarea name="ads_content" class="form-control" rows="4">{{ $pop->ads_content ?? '' }}</textarea>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
    </form>
</div>
@endsection
