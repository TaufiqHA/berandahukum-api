@extends('layouts.front')

@section('content')
<div class="wrap">
    <div class="layout">
        <div class="main">
            <div class="section__head">
                <span class="section__no">&bull;</span>
                <h1 class="section__title" style="font-size:1rem;">Hasil Pencarian</h1>
                <span class="section__rule"></span>
            </div>
            <p class="muted">{{ $heading }}</p>

            @if (count($articles))
                <div class="grid grid--2">
                    @foreach ($articles as $a)
                        <article class="story">
                            <a class="story__media" href="{{ site_url('a/'.$a['article_uri']) }}">
                                <img src="{{ article_image($a['article_img'] ?? null) }}" alt="{{ $a['article_title'] }}" loading="lazy">
                            </a>
                            <div class="story__body">
                                <h3 class="story__title"><a href="{{ site_url('a/'.$a['article_uri']) }}">{{ $a['article_title'] }}</a></h3>
                                <p class="story__meta"><time>{{ format_tanggal($a['article_date'] ?? '', 'ins') }}</time></p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="empty">
                    <h3>Tidak ada hasil</h3>
                    <p>Tidak ditemukan artikel untuk &ldquo;{{ $q }}&rdquo;. Coba kata kunci lain.</p>
                </div>
            @endif

            @include('partials.pager', ['paginator' => $articles])
        </div>
        @include('partials.side')
    </div>
</div>
@endsection
