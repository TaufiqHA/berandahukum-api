@php
    $level = auth()->user()->user_level ?? '';
    $is = fn (string $p) => request()->is('admin/'.$p) || request()->is('admin/'.$p.'/*');
@endphp
<aside class="admin-sidebar" id="adminSidebar">
    <a class="admin-brand" href="{{ site_admin() }}">
        <img src="{{ base_url('berandahukum.svg') }}" alt="Beranda Hukum">
    </a>

    <nav class="admin-nav" aria-label="Navigasi admin">
        <div class="admin-nav__group">
            <div class="admin-nav__label">Utama</div>
            <a href="{{ site_admin() }}" class="{{ request()->is('admin') || request()->is('admin/dashboard') ? 'is-active' : '' }}"><i class="fa fa-dashboard"></i> Dashboard</a>
        </div>

        <div class="admin-nav__group">
            <div class="admin-nav__label">Konten</div>
            <a href="{{ site_admin('article') }}" class="{{ $is('article') ? 'is-active' : '' }}"><i class="fa fa-file-text-o"></i> Artikel</a>
            <a href="{{ site_admin('ads') }}" class="{{ $is('ads') ? 'is-active' : '' }}"><i class="fa fa-bullhorn"></i> Iklan</a>
            <a href="{{ site_admin('banner') }}" class="{{ $is('banner') ? 'is-active' : '' }}"><i class="fa fa-picture-o"></i> Banner</a>
            <a href="{{ site_admin('quotes') }}" class="{{ $is('quotes') ? 'is-active' : '' }}"><i class="fa fa-quote-right"></i> Quote</a>
        </div>

        <div class="admin-nav__group">
            <div class="admin-nav__label">Interaksi</div>
            <a href="{{ site_admin('pertanyaan') }}" class="{{ $is('pertanyaan') ? 'is-active' : '' }}"><i class="fa fa-question-circle-o"></i> Pertanyaan</a>
            <a href="{{ site_admin('komentar') }}" class="{{ $is('komentar') ? 'is-active' : '' }}"><i class="fa fa-comments-o"></i> Komentar</a>
            <a href="{{ site_admin('contact') }}" class="{{ $is('contact') ? 'is-active' : '' }}"><i class="fa fa-envelope-o"></i> Kontak</a>
        </div>

        @if ($level === 'admin')
            <div class="admin-nav__group">
                <div class="admin-nav__label">Data Master</div>
                <a href="{{ site_admin('label') }}" class="{{ $is('label') ? 'is-active' : '' }}"><i class="fa fa-tags"></i> Label</a>
                <a href="{{ site_admin('category') }}" class="{{ $is('category') ? 'is-active' : '' }}"><i class="fa fa-folder-o"></i> Kategori</a>
                <a href="{{ site_admin('sub-category') }}" class="{{ $is('sub-category') ? 'is-active' : '' }}"><i class="fa fa-folder-open-o"></i> Sub Kategori</a>
                <a href="{{ site_admin('user') }}" class="{{ $is('user') ? 'is-active' : '' }}"><i class="fa fa-users"></i> Pengguna</a>
            </div>

            <div class="admin-nav__group">
                <div class="admin-nav__label">Setting</div>
                <a href="{{ site_admin('popup') }}" class="{{ $is('popup') ? 'is-active' : '' }}"><i class="fa fa-window-restore"></i> Iklan PopUp</a>
                <a href="{{ site_admin('menu') }}" class="{{ $is('menu') ? 'is-active' : '' }}"><i class="fa fa-bars"></i> Menu</a>
                <a href="{{ site_admin('pilihan') }}" class="{{ $is('pilihan') ? 'is-active' : '' }}"><i class="fa fa-star-o"></i> Artikel Pilihan</a>
                <a href="{{ site_admin('settingscategory') }}" class="{{ $is('settingscategory') ? 'is-active' : '' }}"><i class="fa fa-sort-numeric-asc"></i> Urutan Kategori</a>
                <a href="{{ site_admin('settings') }}" class="{{ $is('settings') ? 'is-active' : '' }}"><i class="fa fa-info-circle"></i> Informasi</a>
                <a href="{{ site_admin('sosialmedia') }}" class="{{ $is('sosialmedia') ? 'is-active' : '' }}"><i class="fa fa-share-alt"></i> Sosial Media</a>
                <a href="{{ site_admin('settingemail/edit') }}" class="{{ $is('settingemail') ? 'is-active' : '' }}"><i class="fa fa-envelope"></i> Setting Email</a>
            </div>
        @endif
    </nav>
</aside>
