@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('sosialmedia/edit/'.$row->setting_id) : site_admin('sosialmedia/add') }}">
        @csrf
        <div class="form-group">
            <label>Nama (contoh: facebook, twitter, instagram)</label>
            <input type="text" name="settingName" class="form-control" value="{{ old('settingName', $row->setting_name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Link</label>
            <input type="text" name="settingContent" class="form-control" value="{{ old('settingContent', $row->setting_content ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="settingSort" class="form-control" value="{{ old('settingSort', $row->urutan ?? 0) }}">
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('sosialmedia') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
