<footer class="site-footer">
    <div class="wrap site-footer__grid">
        <div class="footer-brand">
            <img src="{{ base_url('berandahukum.svg') }}" alt="Beranda Hukum">
            <p class="muted small">Wadah untuk belajar hukum, menambah wawasan, dan berbagi tentang hukum.</p>
        </div>

        <div class="footer-col footer-col--info">
            <h2>Informasi</h2>
            <ul>
                @foreach ($frontFooterInfo as $r)
                    <li><a href="{{ site_url('informasi/'.md5($r['setting_id'])) }}">{{ $r['setting_name'] }}</a></li>
                @endforeach
                <li><a href="{{ site_url('pertanyaan') }}">Daftar Pertanyaan</a></li>
                <li><a href="{{ site_url('contact') }}">Kontak</a></li>
            </ul>
        </div>

        <div class="footer-col footer-col--follow">
            <h2>Ikuti Kami</h2>
            <div class="footer-sosial">
                @foreach ($frontFooterSosial as $f)
                    <a href="{{ $f['setting_content'] }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($f['setting_name']) }}">
                        <span class="fa fa-{{ $f['setting_name'] }}" aria-hidden="true"></span>
                    </a>
                @endforeach
                <a href="{{ site_url('rss') }}" target="_blank" rel="noopener" aria-label="RSS"><span class="fa fa-rss" aria-hidden="true"></span></a>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="wrap">copyright &copy; 2014 - {{ date('Y') }} berandahukum.com</div>
    </div>
</footer>
