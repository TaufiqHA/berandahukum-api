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
            <label>Tipe Banner</label>
            <select name="bannerType" id="bannerType" class="form-control">
                <option value="0" @selected(($row->banner_type ?? 0) == 0)>Gambar</option>
                <option value="1" @selected(($row->banner_type ?? 0) == 1)>Iframe / Embed</option>
                <option value="2" @selected(($row->banner_type ?? 0) == 2)>Script / HTML</option>
            </select>
        </div>
        <div class="form-group" id="field-link">
            <label>Link URL (untuk tipe Gambar)</label>
            <input type="text" name="bannerLink" class="form-control" value="{{ old('bannerLink', $row->link_url ?? '') }}">
        </div>
        <div class="form-group" id="field-image">
            <label>Gambar (untuk tipe Gambar)</label>
            @include('partials.file_field', ['name' => 'file_banner', 'accept' => 'image/*', 'current' => $row->file_banner ?? null])
        </div>
        <div class="form-group" id="field-content">
            <label>URL Iframe / Kode Script (untuk tipe Iframe atau Script)</label>
            <textarea name="bannerContent" class="form-control" rows="6" placeholder="Tempel URL embed atau kode HTML/script di sini">{{ old('bannerContent', $row->banner_content ?? '') }}</textarea>
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
    function toggle() {
        var gambar = sel.value === '0';
        document.getElementById('field-link').style.display = gambar ? '' : 'none';
        document.getElementById('field-image').style.display = gambar ? '' : 'none';
        document.getElementById('field-content').style.display = gambar ? 'none' : '';
    }
    sel.addEventListener('change', toggle);
    toggle();
})();
</script>
@endsection
