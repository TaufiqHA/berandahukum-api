<header class="site-header">
    <div class="site-header__top">
        <div class="wrap site-header__bar">
            <a class="brand" href="{{ site_url() }}">
                <img src="{{ base_url('berandahukum.svg') }}" alt="Beranda Hukum — Media Belajar Hukum">
            </a>

            <nav class="nav" id="primary-nav" aria-label="Menu utama">
                @foreach ($frontMenuLinks as $item)
                    <a href="{{ $item['url'] }}" class="{{ $item['active'] ? 'is-active' : '' }}">{{ $item['title'] }}</a>
                @endforeach
            </nav>

            <form class="search" action="{{ site_url('search') }}" role="search">
                <label class="visually-hidden" for="q">Cari artikel</label>
                <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="Cari artikel…">
                <button type="submit" aria-label="Cari">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                </button>
            </form>
        </div>
    </div>

    @if (!empty($frontCategoriesTree))
        <div class="sectionbar">
            <div class="wrap sectionbar__inner">
                <span class="kicker">Rubrik</span>
                @foreach ($frontCategoriesTree as $cat)
                    <a href="{{ site_url('k/'.$cat['uri']) }}">{{ $cat['name'] }}</a>
                @endforeach
            </div>
        </div>
    @endif
</header>
