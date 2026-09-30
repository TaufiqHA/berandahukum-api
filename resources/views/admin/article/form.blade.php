@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('article/edit/'.$row->article_id) : site_admin('article/add') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Judul <span class="text-danger">*</span></label>
            <input type="text" name="articleTitle" maxlength="100" class="form-control @error('articleTitle') is-invalid @enderror" value="{{ old('articleTitle', $row->article_title ?? '') }}" required>
            @error('articleTitle')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Wajib diisi, maksimal 100 karakter (dipakai juga sebagai URL artikel).</small>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Label</label>
                    <select name="labelId" class="form-control @error('labelId') is-invalid @enderror">
                        <option value="0">-</option>
                        @foreach ($labels as $l)
                            <option value="{{ $l->label_id }}" @selected(old('labelId', $row->label_id ?? 0) == $l->label_id)>{{ $l->label_name }}</option>
                        @endforeach
                    </select>
                    @error('labelId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="categoryId" class="form-control @error('categoryId') is-invalid @enderror">
                        <option value="">-</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->category_id }}" @selected(old('categoryId', $selectedCategory) == $c->category_id)>{{ $c->category_name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Sub Kategori</label>
                    <select name="subCategoryId" class="form-control @error('subCategoryId') is-invalid @enderror">
                        <option value="0">-</option>
                        @foreach ($subCategories as $sc)
                            <option value="{{ $sc->sub_category_id }}" @selected(old('subCategoryId', $selectedSub) == $sc->sub_category_id)>{{ $sc->sub_category_name }}</option>
                        @endforeach
                    </select>
                    @error('subCategoryId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Penulis <span class="text-danger">*</span></label>
                    <input type="text" name="articleAuthor" maxlength="100" class="form-control @error('articleAuthor') is-invalid @enderror" value="{{ old('articleAuthor', $row->article_author ?? '') }}" required>
                    @error('articleAuthor')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tanggal Artikel</label>
                    <input type="text" name="articleDate" class="form-control @error('articleDate') is-invalid @enderror" value="{{ old('articleDate', $row->article_date ?? date('Y-m-d H:i:s')) }}">
                    @error('articleDate')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Tanggal Buat</label>
                    <input type="date" name="createDate" class="form-control @error('createDate') is-invalid @enderror" value="{{ old('createDate', $row->create_date ?? date('Y-m-d')) }}">
                    @error('createDate')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Gambar</label>
                    @include('partials.file_field', ['name' => 'articleImage', 'accept' => 'image/*', 'current' => $row->article_img ?? null])
                    @error('articleImage')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Format gambar, maksimal 2 MB.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>PDF</label>
                    @include('partials.file_field', ['name' => 'articlePdf', 'accept' => 'application/pdf', 'current' => $row->article_pdf ?? null])
                    @error('articlePdf')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Format PDF, maksimal 10 MB.</small>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="articleStatus" class="form-control @error('articleStatus') is-invalid @enderror">
                <option value="1" @selected(old('articleStatus', $row->article_status ?? 1) == 1)>Publish</option>
                <option value="2" @selected(old('articleStatus', $row->article_status ?? 1) == 2)>Draft</option>
                <option value="0" @selected(old('articleStatus', $row->article_status ?? 1) == 0)>Tidak aktif</option>
            </select>
            @error('articleStatus')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="headline" value="1" @checked(old('headline', $row->headline_news ?? 0) == 1)> Jadikan Headline</label>
        </div>
        <div class="form-group">
            <label>Konten</label>
            <textarea name="articleContent" id="articleEditor" class="form-control @error('articleContent') is-invalid @enderror" rows="12">{{ old('articleContent', $row->article_content ?? '') }}</textarea>
            @error('articleContent')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('article') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
