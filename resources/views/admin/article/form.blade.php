@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('article/edit/'.$row->article_id) : site_admin('article/add') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="articleTitle" class="form-control" value="{{ old('articleTitle', $row->article_title ?? '') }}" required>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Label</label>
                    <select name="labelId" class="form-control">
                        <option value="0">-</option>
                        @foreach ($labels as $l)
                            <option value="{{ $l->label_id }}" @selected(($row->label_id ?? 0) == $l->label_id)>{{ $l->label_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="categoryId" class="form-control">
                        <option value="">-</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->category_id }}" @selected($selectedCategory == $c->category_id)>{{ $c->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Sub Kategori</label>
                    <select name="subCategoryId" class="form-control">
                        <option value="0">-</option>
                        @foreach ($subCategories as $sc)
                            <option value="{{ $sc->sub_category_id }}" @selected($selectedSub == $sc->sub_category_id)>{{ $sc->sub_category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Penulis</label>
                    <input type="text" name="articleAuthor" class="form-control" value="{{ old('articleAuthor', $row->article_author ?? '') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tanggal Artikel</label>
                    <input type="text" name="articleDate" class="form-control" value="{{ old('articleDate', $row->article_date ?? date('Y-m-d H:i:s')) }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tanggal Buat</label>
                    <input type="date" name="createDate" class="form-control" value="{{ old('createDate', $row->create_date ?? date('Y-m-d')) }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="articleImage" class="form-control" accept="image/*">
                    @if (!empty($row->article_img))<small>File saat ini: {{ $row->article_img }}</small>@endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>PDF</label>
                    <input type="file" name="articlePdf" class="form-control" accept="application/pdf">
                    @if (!empty($row->article_pdf))<small>File saat ini: {{ $row->article_pdf }}</small>@endif
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="articleStatus" class="form-control">
                <option value="1" @selected(($row->article_status ?? 1) == 1)>Publish</option>
                <option value="2" @selected(($row->article_status ?? 1) == 2)>Draft</option>
                <option value="0" @selected(($row->article_status ?? 1) == 0)>Tidak aktif</option>
            </select>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="headline" value="1" @checked(($row->headline_news ?? 0) == 1)> Jadikan Headline</label>
        </div>
        <div class="form-group">
            <label>Konten</label>
            <textarea name="articleContent" id="articleEditor" class="form-control" rows="12">{{ old('articleContent', $row->article_content ?? '') }}</textarea>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('article') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
