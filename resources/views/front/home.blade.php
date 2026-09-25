@extends('layouts.front')

@section('content')
@php
    $promos = collect([8, 9, 10, 16, 17, 18, 19, 20])
        ->map(fn ($p) => $frontService->getArticleById($p))
        ->filter()
        ->values();
    $layout = $frontHomeLayout ?? [];
@endphp

<div class="home">
    @foreach ($layout as $sec)
        @if (!($sec['enabled'] ?? true))
            @continue
        @endif

        @switch($sec['key'])
            @case('banner_atas')
                <div class="wrap home__top-ad">
                    @include('partials.ad', ['ad' => $frontService->getArticleById(2)])
                </div>
                @break

            @case('slider')
                <div class="wrap home__hero">
                    @include('partials.slider_atas', ['art_pilihan_top' => $art_pilihan_top ?? []])
                </div>
                @break

            @case('iklan_atas')
                <div class="wrap home-extra">
                    @include('partials.ad', ['ad' => $frontService->getArticleById(2)])
                </div>
                @break

            @case('hash')
                <div class="wrap home__hash">
                    <div class="divider-hash"><span>#</span></div>
                    @include('partials.ad', ['ad' => $frontService->getArticleById(12)])
                </div>
                @break

            @case('terbaru')
                <section class="band band--tint home-extra">
                    <div class="wrap">
                        <div class="section__head">
                            <span class="section__no">01</span>
                            <h2 class="section__title">Terbaru</h2>
                            <span class="section__rule"></span>
                            <a class="section__more" href="{{ site_url('search') }}">Cari</a>
                        </div>
                        @if (!empty($article))
                            <div class="grid grid--3">
                                @foreach ($article as $a)
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
                            <div class="empty"><h3>Belum ada artikel</h3><p>Artikel akan tampil setelah dipublikasikan.</p></div>
                        @endif
                    </div>
                </section>
                @break

            @case('tiles')
                @if ($promos->isNotEmpty())
                    <section class="band home__tiles">
                        <div class="wrap">
                            <div class="section__head">
                                <span class="section__no">&bull;</span>
                                <h2 class="section__title">Buku &amp; Layanan</h2>
                                <span class="section__rule"></span>
                            </div>
                            <div class="grid grid--3">
                                @foreach ($promos as $ad)
                                    <div>@include('partials.ad', ['ad' => $ad])</div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
                @break

            @case('categories')
                @if (!empty($frontCategoriesTree))
                    <section class="band band--tint home__cats">
                        <div class="wrap">
                            <div class="section__head">
                                <span class="section__no">&bull;</span>
                                <h2 class="section__title">Jelajahi Kategori</h2>
                                <span class="section__rule"></span>
                            </div>
                            <div class="grid grid--4 cats-desktop">
                                @foreach ($frontCategoriesTree as $cat)
                                    <div class="widget" style="border-top:0;padding-top:0;">
                                        <h2 style="margin-bottom:var(--s2);"><a href="{{ site_url('k/'.$cat['uri']) }}">{{ $cat['name'] }}</a></h2>
                                        @if (!empty($cat['subs']))
                                            <ul>
                                                @foreach (array_slice($cat['subs'], 0, 6) as $sub)
                                                    <li><a href="{{ site_url('s/'.$sub['uri']) }}" style="font-size:.88rem;color:var(--n-600);">{{ $sub['name'] }}</a></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @include('partials.category_cards')
                        </div>
                    </section>
                @endif
                @break

            @case('headline')
                @if (!empty($tmpHeadLine))
                    <section class="band home-extra">
                        <div class="wrap">
                            <div class="section__head">
                                <span class="section__no">02</span>
                                <h2 class="section__title">Headline</h2>
                                <span class="section__rule"></span>
                            </div>
                            <div class="grid grid--3">
                                @foreach ($tmpHeadLine as $h)
                                    <article class="story">
                                        <a class="story__media" href="{{ site_url('a/'.$h['article_uri']) }}">
                                            <img src="{{ article_image($h['article_img'] ?? null) }}" alt="{{ $h['article_title'] }}" loading="lazy">
                                        </a>
                                        <div class="story__body">
                                            <h3 class="story__title"><a href="{{ site_url('a/'.$h['article_uri']) }}">{{ $h['article_title'] }}</a></h3>
                                            <p class="story__meta">
                                                <time>{{ format_tanggal($h['article_date'] ?? '', 'ins') }}</time>
                                                @if (!empty($h['article_author']))<span>&middot;</span><span>{{ $h['article_author'] }}</span>@endif
                                            </p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <div class="grid grid--2" style="margin-top:var(--s6);">
                                @include('partials.ad', ['ad' => $frontService->getArticleById(3)])
                                @include('partials.ad', ['ad' => $frontService->getArticleById(4)])
                            </div>
                        </div>
                    </section>
                @endif
                @break

            @case('pilihan_editor')
                @if (!empty($article_pilihanatas) || !empty($article_pilihanbawah))
                    <section class="band band--tint home-extra">
                        <div class="wrap">
                            <div class="section__head">
                                <span class="section__no">03</span>
                                <h2 class="section__title">Pilihan Editor</h2>
                                <span class="section__rule"></span>
                            </div>
                            <div class="grid grid--3">
                                @foreach (array_merge($article_pilihanatas ?? [], $article_pilihanbawah ?? []) as $a)
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
                        </div>
                    </section>
                @endif
                @break

            @case('label')
                @foreach ($label as $l)
                    @if (($l['jumlah'] ?? 0) > 0)
                        @php $detail = $frontService->articlesByLabel((int) $l['label_id'], 4); @endphp
                        <section class="band home-extra">
                            <div class="wrap">
                                <div class="section__head">
                                    <span class="section__no">&bull;</span>
                                    <h2 class="section__title">{{ $l['label_name'] }}</h2>
                                    <span class="section__rule"></span>
                                    <a class="section__more" href="{{ site_url('l/'.$l['label_uri']) }}">Lihat semua</a>
                                </div>
                                <div class="grid grid--4">
                                    @foreach ($detail as $a)
                                        <article class="story">
                                            <a class="story__media" href="{{ site_url('a/'.$a['article_uri']) }}">
                                                <img src="{{ article_image($a['article_img'] ?? null) }}" alt="{{ $a['article_title'] }}" loading="lazy">
                                            </a>
                                            <div class="story__body">
                                                <h3 class="story__title" style="font-size:1.02rem;"><a href="{{ site_url('a/'.$a['article_uri']) }}">{{ $a['article_title'] }}</a></h3>
                                                <p class="story__meta"><time>{{ format_tanggal($a['article_date'] ?? '', 'ins') }}</time></p>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    @endif
                @endforeach
                @break

            @case('most_read')
                <section class="band band--tint home-extra">
                    <div class="wrap">
                        <div class="grid grid--2">
                            <div>
                                <div class="section__head">
                                    <span class="section__no">&bull;</span>
                                    <h2 class="section__title">Paling Banyak Dibaca</h2>
                                    <span class="section__rule"></span>
                                </div>
                                <ol class="ranked">
                                    @foreach ($frontService->mostView() as $a)
                                        <li><a href="{{ site_url('a/'.$a['article_uri']) }}">{{ $a['article_title'] }}</a></li>
                                    @endforeach
                                </ol>
                            </div>
                            <div>
                                <div class="section__head">
                                    <span class="section__no">&bull;</span>
                                    <h2 class="section__title">Paling Banyak Dikomentari</h2>
                                    <span class="section__rule"></span>
                                </div>
                                <ol class="ranked">
                                    @foreach ($frontService->mostComment() as $a)
                                        <li><a href="{{ site_url('a/'.$a['article_uri']) }}">{{ $a['article_title'] }}</a></li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="home-extra">
                    @include('partials.ad', ['ad' => $frontService->getArticleById(5)])
                    @include('partials.ad', ['ad' => $frontService->getArticleById(6)])
                </div>
                @break

            @case('quote')
                @if (count($frontService->quotes()))
                    <section class="band band--dark home-extra">
                        <div class="wrap">
                            <div class="section__head">
                                <span class="section__no" style="color:var(--brand);">&bull;</span>
                                <h2 class="section__title">Quote</h2>
                                <span class="section__rule" style="background:rgba(255,255,255,.2);"></span>
                            </div>
                            <div class="grid grid--3">
                                @foreach ($frontService->quotes() as $q)
                                    <img src="{{ url('uploads/img/'.$q['quote_image']) }}" alt="Quote" loading="lazy">
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
                <div class="home-extra">
                    @include('partials.ad', ['ad' => $frontService->getArticleById(7)])
                    @include('partials.ad', ['ad' => $frontService->getArticleById(14)])
                    @include('partials.ad', ['ad' => $frontService->getArticleById(21)])
                    @include('partials.ad', ['ad' => $frontService->getArticleById(22)])
                    @include('partials.ad', ['ad' => $frontService->getArticleById(23)])
                </div>
                @break

            @case('youtube')
                @if (($frontSys['show_youtube'] ?? null) === 'yes')
                    <section class="band band--tint home-extra">
                        <div class="wrap">
                            <div class="section__head">
                                <span class="section__no">&bull;</span>
                                <h2 class="section__title">Youtube Beranda Hukum</h2>
                                <span class="section__rule"></span>
                            </div>
                            <div style="position:relative;aspect-ratio:16/9;">
                                <iframe src="https://www.youtube.com/embed/hyQVj1RbC-s" style="position:absolute;inset:0;width:100%;height:100%;border:0;" title="Youtube Beranda Hukum" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    </section>
                @endif
                @break

            @case('mitra')
                <div class="home-extra">
                    @include('partials.slider_bawah')
                </div>
                @break

            @case('iklan_bawah')
                <div class="wrap home__foot-ad">
                    @include('partials.ad', ['ad' => $frontService->getArticleById(7)])
                </div>
                @break
        @endswitch
    @endforeach
</div>
@endsection
