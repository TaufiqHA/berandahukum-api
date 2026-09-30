@extends('layouts.admin')
@section('title', $title)
@section('content')
@php
    $adsTypeOld = (int) old('adsType', $row->ads_type ?? 0);
    $adsFileTypeOld = (int) old('adsFileType', $row->ads_file_type ?? 0);
    $adsUrlValue = old('adsUrl', $row->ads_url ?? '');
@endphp
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('ads/edit/'.md5($row->ads_id)) : site_admin('ads/add') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Posisi</label>
            <input type="number" name="adsPosition" id="adsPosition" min="1" max="23" list="adsPositionList" class="form-control @error('adsPosition') is-invalid @enderror" value="{{ old('adsPosition', $row->ads_position ?? '') }}" required>
            <datalist id="adsPositionList">
                @foreach (($positions ?? []) as $pos => $keterangan)
                    <option value="{{ $pos }}">{{ $keterangan }}</option>
                @endforeach
            </datalist>
            <small class="form-text text-muted" id="adsPositionInfo">Nomor posisi menentukan letak iklan. Lihat daftar keterangan di bawah.</small>
            @error('adsPosition')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            <details class="mt-2">
                <summary style="cursor:pointer;">Daftar keterangan posisi</summary>
                <div class="table-responsive mt-2">
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr><th style="width:70px;">Posisi</th><th>Letak Iklan</th></tr>
                        </thead>
                        <tbody>
                            @foreach (($positions ?? []) as $pos => $keterangan)
                                <tr @class(['table-warning' => str_starts_with($keterangan, 'Tidak digunakan')])>
                                    <td class="text-center">{{ $pos }}</td>
                                    <td>{{ $keterangan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
        <div class="form-group">
            <label>Tipe Iklan</label>
            <select name="adsType" id="adsType" class="form-control @error('adsType') is-invalid @enderror">
                <option value="0" @selected($adsTypeOld === 0)>Gambar Upload</option>
                <option value="1" @selected($adsTypeOld === 1)>URL / Embed</option>
            </select>
            @error('adsType')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Tipe File</label>
            <select name="adsFileType" id="adsFileType" class="form-control @error('adsFileType') is-invalid @enderror">
                <option value="0" @selected($adsFileTypeOld === 0)>Gambar</option>
                <option value="1" @selected($adsFileTypeOld === 1)>Iframe</option>
                <option value="2" @selected($adsFileTypeOld === 2)>HTML / Kode</option>
            </select>
            @error('adsFileType')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Untuk Gambar Upload, tipe file harus Gambar.</small>
        </div>
        <div class="form-group" id="field-ads-link">
            <label>Link</label>
            <input type="text" name="ads_link" id="adsLinkInput" class="form-control @error('ads_link') is-invalid @enderror" value="{{ old('ads_link', $row->ads_link ?? '#') }}">
            @error('ads_link')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group" id="field-ads-image">
            <label>Upload Gambar</label>
            @include('partials.file_field', ['name' => 'adsFile', 'accept' => 'image/*', 'current' => $adsTypeOld === 0 ? ($row->ads_url ?? null) : null])
            @error('adsFile')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Format gambar, maksimal 2 MB.</small>
        </div>
        <div class="form-group" id="field-ads-url">
            <label>URL / Embed</label>
            <input type="text" name="adsUrl" id="adsUrlInput" class="form-control @error('adsUrl') is-invalid @enderror" value="{{ $adsFileTypeOld === 2 ? '' : $adsUrlValue }}">
            @error('adsUrl')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group" id="field-ads-code">
            <label>Kode HTML / Script</label>
            <textarea name="adsUrl" id="adsUrlCode" class="form-control @error('adsUrl') is-invalid @enderror" rows="6" placeholder="Tempel kode iklan lengkap (termasuk tag &lt;script&gt; dan &lt;ins&gt;) di sini">{{ $adsFileTypeOld === 2 ? $adsUrlValue : '' }}</textarea>
            @error('adsUrl')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Khusus tipe HTML / Kode, tempel kode iklan lengkap (termasuk tag &lt;script&gt;) di sini — jangan di kolom URL / Embed.</small>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('ads') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
<script>
(function () {
    var typeSel = document.getElementById('adsType');
    var fileSel = document.getElementById('adsFileType');
    if (!typeSel || !fileSel) return;

    var fields = {
        image: document.getElementById('field-ads-image'),
        url: document.getElementById('field-ads-url'),
        code: document.getElementById('field-ads-code'),
        link: document.getElementById('field-ads-link'),
    };
    var inputs = {
        url: document.getElementById('adsUrlInput'),
        code: document.getElementById('adsUrlCode'),
        link: document.getElementById('adsLinkInput'),
        file: document.querySelector('#field-ads-image input[type="file"]'),
    };

    function show(el, visible) {
        if (el) el.style.display = visible ? '' : 'none';
    }

    function toggle() {
        var isUpload = typeSel.value === '0';
        var isCode = fileSel.value === '2';

        // Gambar hanya untuk Gambar Upload; URL untuk URL/Embed non-kode;
        // textarea kode untuk tipe HTML / Kode.
        show(fields.image, isUpload && !isCode);
        show(fields.url, !isUpload && !isCode);
        show(fields.code, isCode);
        // Iklan HTML / script tidak memakai tautan tujuan (tautan ada di kodenya).
        show(fields.link, !isCode);

        // Nonaktifkan kolom yang tersembunyi agar tidak ikut terkirim.
        if (inputs.file) inputs.file.disabled = !(isUpload && !isCode);
        if (inputs.url) inputs.url.disabled = isUpload || isCode;
        if (inputs.code) inputs.code.disabled = !isCode;
        if (inputs.link) inputs.link.disabled = isCode;
    }

    typeSel.addEventListener('change', toggle);
    fileSel.addEventListener('change', toggle);
    toggle();
})();
</script>
<script>
(function () {
    var input = document.getElementById('adsPosition');
    var info = document.getElementById('adsPositionInfo');
    if (!input || !info) return;

    var positions = @json($positions ?? []);
    var hint = 'Nomor posisi menentukan letak iklan. Lihat daftar keterangan di bawah.';

    function update() {
        var label = positions[input.value];
        info.textContent = label ? 'Posisi ' + input.value + ' — ' + label : hint;
    }

    input.addEventListener('input', update);
    input.addEventListener('change', update);
    update();
})();
</script>
@endsection
