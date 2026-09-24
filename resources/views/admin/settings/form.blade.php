@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('settings/edit/'.$row->setting_id) : site_admin('settings/add') }}">
        @csrf
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="settingName" class="form-control" value="{{ old('settingName', $row->setting_name ?? '') }}">
        </div>
        <div class="form-group">
            <label>Isi</label>
            <textarea name="settingContent" id="articleEditor" class="form-control" rows="8">{{ old('settingContent', $row->setting_content ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="settingSort" class="form-control" value="{{ old('settingSort', $row->urutan ?? 0) }}">
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('settings') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
