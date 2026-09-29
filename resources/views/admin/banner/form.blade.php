@extends('layouts.admin')
@section('title', $title)
@section('content')
@php
    $bannerTypeOld = old('bannerType', $row->banner_type ?? 0);
    $bannerLinkValue = old('bannerLink', $row->link_url ?? '');
    $bannerContentView = old('bannerContent', $row->banner_content ?? '');

    // Data lama menyimpan kode script/iframe di link_url. Tampilkan pada
    // textarea konten agar tersimpan di kolom yang benar saat disimpan ulang.
    if (trim((string) $bannerContentView) === '' && (int) $bannerTypeOld !== 0
        && $bannerLinkValue !== '' && preg_match('/<[a-z!\/]/i', (string) $bannerLinkValue)) {
        $bannerContentView = $bannerLinkValue;
        $bannerLinkValue = '';
    }
@endphp
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('banner/edit/'.md5($row->id_banner)) : site_admin('banner/add') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Nama Banner</label>
            <input type="text" name="bannerName" class="form-control" value="{{ old('bannerName', $row->nama_banner ?? '') }}">
        </div>
        <div class="form-group">
            <label>Tipe Banner</label>
            <select name="bannerType" id="bannerType" class="form-control">
                <option value="0" @selected((int) $bannerTypeOld === 0)>Gambar</option>
                <option value="1" @selected((int) $bannerTypeOld === 1)>Iframe / Embed</option>
                <option value="2" @selected((int) $bannerTypeOld === 2)>Script / HTML</option>
            </select>
        </div>
        <div class="form-group" id="field-link">
            <label>Link URL (untuk tipe Gambar)</label>
            <input type="text" name="bannerLink" class="form-control" value="{{ $bannerLinkValue }}">
        </div>
        <div class="form-group" id="field-image">
            <label>Gambar (untuk tipe Gambar)</label>
            @include('partials.file_field', ['name' => 'file_banner', 'accept' => 'image/*', 'current' => $row->file_banner ?? null])
        </div>
        <div class="form-group" id="field-content">
            <label>URL Iframe / Kode Script (untuk tipe Iframe atau Script)</label>
            <textarea name="bannerContent" id="bannerContent" class="form-control" rows="6" placeholder="Tempel URL embed atau kode HTML/script di sini">{{ $bannerContentView }}</textarea>
            <small class="form-text text-muted">Khusus tipe Script / HTML, tempel kode iklan lengkap (termasuk tag &lt;script&gt; dan &lt;ins&gt;) di sini — jangan di kolom Link URL.</small>
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
<script>
(function () {
    var sel = document.getElementById('bannerType');
    if (!sel) return;
    var content = document.getElementById('bannerContent');
    function toggle() {
        var gambar = sel.value === '0';
        document.getElementById('field-link').style.display = gambar ? '' : 'none';
        document.getElementById('field-image').style.display = gambar ? '' : 'none';
        document.getElementById('field-content').style.display = gambar ? 'none' : '';
        // Hanya wajibkan kode/URL saat tipenya memang Iframe atau Script.
        if (content) content.required = !gambar;
    }
    sel.addEventListener('change', toggle);
    toggle();
})();
</script>
@endsection
