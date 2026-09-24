@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('sub-category/edit/'.$row->sub_category_id) : site_admin('sub-category/add') }}">
        @csrf
        <div class="form-group">
            <label>Kategori</label>
            <select name="categoryName" class="form-control" required>
                <option value="">-- pilih --</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->category_id }}" @selected(($row->category_id ?? '') == $c->category_id)>{{ $c->category_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Nama Sub Kategori</label>
            <input type="text" name="subCategoryName" class="form-control" value="{{ old('subCategoryName', $row->sub_category_name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Tanggal Buat</label>
            <input type="date" name="subCategoryCreateTime" class="form-control" value="{{ old('subCategoryCreateTime', $row->create_date ?? date('Y-m-d')) }}">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="subcategoryShow" value="yes" @checked(($row->sub_category_show ?? 'yes') === 'yes')> Tampilkan</label>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('sub-category') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
