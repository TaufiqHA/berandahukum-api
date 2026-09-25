@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="mb-0">{{ $title }}</h4>
        <a href="{{ site_admin('slider/add') }}" class="btn btn-primary btn-sm">Tambah Artikel</a>
    </div>
    <p class="text-muted">Atur artikel yang tampil pada slider (sorotan) beranda. Seret untuk mengubah urutan, lalu klik <strong>Simpan Urutan</strong>.</p>

    <input type="hidden" id="save_url" value="{{ site_admin('slider/urutan') }}">

    @if (count($datapilihan))
        <ul id="sortable" class="list-unstyled slider-sort">
            @foreach ($datapilihan as $r)
                <li id="{{ $r['id'] }}" class="slider-sort__item">
                    <span class="slider-sort__handle" title="Seret untuk mengurutkan"><i class="fa fa-bars"></i></span>
                    <span class="slider-sort__no">{{ $loop->iteration }}</span>
                    <span class="slider-sort__title">{{ $r['article_title'] }}</span>
                    <span class="slider-sort__actions">
                        <a href="{{ site_admin('slider/edit/'.$r['id']) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                        <a href="{{ site_admin('slider/delete/'.$r['id']) }}" onclick="return confirm('Hapus artikel ini dari slider?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                    </span>
                </li>
            @endforeach
        </ul>
        <button type="button" id="save_btn" class="btn btn-success btn-sm">Simpan Urutan</button>
    @else
        <div class="alert alert-info">Belum ada artikel pada slider. Klik <strong>Tambah Artikel</strong> untuk menambahkan.</div>
    @endif
</div>

<style>
    .slider-sort__item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; margin-bottom: 8px; background: #fff; border: 1px solid #dee2e6; border-left: 4px solid var(--brand, #c1121f); }
    .slider-sort__handle { cursor: grab; color: #9b9a93; }
    .slider-sort__no { font-weight: 700; color: var(--brand, #c1121f); min-width: 18px; }
    .slider-sort__title { flex: 1 1 auto; min-width: 0; }
    .slider-sort__actions { display: flex; gap: 6px; flex: 0 0 auto; }
    #sortable .ui-state-highlight { height: 44px; margin-bottom: 8px; background: #fbeaea; border: 1px dashed var(--brand, #c1121f); }
</style>
@endsection
