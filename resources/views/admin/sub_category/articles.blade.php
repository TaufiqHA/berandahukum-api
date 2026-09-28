@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('sub-category/urutan') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <p class="text-muted">
        Sub kategori: <strong>{{ $subCategory->sub_category_name }}</strong>.
        Seret artikel untuk mengubah urutannya saat tampil di halaman kategori.
    </p>

    @if ($articles->isEmpty())
        <div class="alert alert-info">Belum ada artikel terbit pada sub kategori ini.</div>
    @else
        <form method="post" action="{{ site_admin('sub-category/articles/'.$subCategory->sub_category_id) }}">
            @csrf
            <input type="hidden" id="save_url" value="{{ site_admin('sub-category/articles/'.$subCategory->sub_category_id) }}">
            <ul id="sortable" class="list-group">
                @foreach ($articles as $a)
                    <li class="list-group-item" id="{{ $a->article_id }}" style="cursor:hand;">
                        <span class="badge bg-secondary">{{ $loop->iteration }}</span>
                        {{ $a->article_title }}
                        <span class="text-muted small">&mdash; {{ $a->article_date }}</span>
                    </li>
                @endforeach
            </ul>
            <button type="button" id="save_btn" class="btn btn-primary mt-3">Simpan Urutan</button>
        </form>
    @endif
</div>
@endsection
