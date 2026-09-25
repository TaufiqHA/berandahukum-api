@if (!empty($art_pilihan_top))
    @php $slides = array_values($art_pilihan_top); $total = count($slides); @endphp
    <section class="slider" id="heroSlider" role="region" aria-roledescription="carousel" aria-label="Sorotan">
        <div class="slider__viewport">
            <div class="slider__track">
                @foreach ($slides as $a)
                    <a class="slide" href="{{ site_url('a/'.$a['article_uri']) }}">
                        <span class="slide__media">
                            <img src="{{ article_image($a['article_img'] ?? null) }}" alt="{{ $a['article_title'] }}" @if (! $loop->first) loading="lazy" @endif>
                        </span>
                        <span class="slide__body">
                            <span class="kicker slide__label">Sorotan</span>
                            <h2>{{ $a['article_title'] }}</h2>
                            <span class="story__meta slide__meta">
                                <time class="slide__date">{{ format_tanggal($a['article_date'] ?? '', 'in') }}</time>
                                @if (!empty($a['article_author']))<span class="slide__sep">&middot;</span><span class="slide__author">{{ $a['article_author'] }}</span>@endif
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>

            @if ($total > 1)
                <button type="button" class="slider__btn slider__prev" aria-label="Sebelumnya">&lsaquo;</button>
                <button type="button" class="slider__btn slider__next" aria-label="Berikutnya">&rsaquo;</button>
            @endif
        </div>

        @if ($total > 1)
            <div class="slider__footer">
                <div class="slider__dots" role="tablist" aria-label="Pilih slide">
                    @foreach ($slides as $i => $a)
                        <button type="button" class="slider__dot" aria-label="Slide {{ $i + 1 }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}"></button>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <script>
        (function () {
            var root = document.getElementById('heroSlider');
            if (!root) return;
            var track = root.querySelector('.slider__track');
            var slides = root.querySelectorAll('.slide');
            var dots = root.querySelectorAll('.slider__dot');
            var prev = root.querySelector('.slider__prev');
            var next = root.querySelector('.slider__next');
            if (slides.length < 2 || !track) return;

            var index = 0, timer = null;

            function go(n) {
                index = (n + slides.length) % slides.length;
                track.style.transform = 'translateX(' + (-index * 100) + '%)';
                dots.forEach(function (d, k) { d.setAttribute('aria-current', k === index ? 'true' : 'false'); });
            }
            function play() { stop(); timer = setInterval(function () { go(index + 1); }, 5000); }
            function stop() { if (timer) { clearInterval(timer); timer = null; } }

            if (prev) prev.addEventListener('click', function () { go(index - 1); });
            if (next) next.addEventListener('click', function () { go(index + 1); });
            dots.forEach(function (d, k) { d.addEventListener('click', function () { go(k); }); });
            root.addEventListener('mouseenter', stop);
            root.addEventListener('mouseleave', play);
            root.addEventListener('focusin', stop);
            root.addEventListener('focusout', play);
            root.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft') { go(index - 1); }
                if (e.key === 'ArrowRight') { go(index + 1); }
            });

            go(0);
            play();
        })();
    </script>
@endif
