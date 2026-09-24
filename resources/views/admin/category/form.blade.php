@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('category/edit/'.$row->category_id) : site_admin('category/add') }}">
        @csrf
        <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="categoryName" class="form-control" value="{{ old('categoryName', $row->category_name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="categoryShow" value="yes" @checked(($row->category_show ?? 'yes') === 'yes')> Tampilkan</label>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('category') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
