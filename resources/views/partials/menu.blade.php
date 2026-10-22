@php
    // Di halaman artikel, pencarian header dipakai untuk mencari kata di dalam
    // artikel yang sedang dibuka (ditangani skrip di article_detail).
    $isArticlePage = request()->is('a/*', 'article/*', 'preview/*');
@endphp
<header class="site-header">
    <div class="site-header__top">
        <div class="wrap site-header__bar">
            <a class="brand" href="{{ site_url() }}">
                <img src="{{ base_url('berandahukum.svg') }}" alt="Beranda Hukum — Media Belajar Hukum">
            </a>

            <nav class="nav" id="primary-nav" aria-label="Menu utama"></nav>

            <form class="search" action="{{ site_url('search') }}" role="search" @if ($isArticlePage) data-article-search @endif>
                <label class="visually-hidden" for="q">{{ $isArticlePage ? 'Cari di artikel' : 'Cari artikel' }}</label>
                <input type="search" id="q" name="q" value="{{ $isArticlePage ? '' : request('q') }}" placeholder="{{ $isArticlePage ? 'Cari di artikel…' : 'Cari artikel…' }}">
                <button type="submit" aria-label="{{ $isArticlePage ? 'Cari di artikel' : 'Cari' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                </button>
            </form>
        </div>
    </div>
</header>
