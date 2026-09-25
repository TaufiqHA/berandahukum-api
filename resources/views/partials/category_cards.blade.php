@if (!empty($frontCategoriesTree))
    <div class="catcards">
        @foreach ($frontCategoriesTree as $cat)
            <section class="catcard">
                <h2 class="catcard__head">
                    <a href="{{ site_url('k/'.$cat['uri']) }}">{{ $cat['name'] }}</a>
                </h2>
                @if (!empty($cat['subs']))
                    <div class="catcard__body">
                        @foreach ($cat['subs'] as $sub)
                            <div class="catcard__item">
                                <button type="button" class="catcard__row" data-sub-uri="{{ $sub['uri'] }}">
                                    <span>{{ $sub['name'] }}</span>
                                    <span class="catcard__chev" aria-hidden="true">&#9662;</span>
                                </button>
                                <div class="catcard__menu" hidden></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </div>

    @once
        <script>
        (function () {
            var api = @json(url('api/v1/subcategories'));
            var articleBase = @json(site_url('a'));

            function esc(s) {
                var d = document.createElement('div');
                d.textContent = s == null ? '' : String(s);
                return d.innerHTML;
            }

            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.catcard__row');
                if (!btn) return;

                var item = btn.closest('.catcard__item');
                var menu = item ? item.querySelector('.catcard__menu') : null;
                if (!menu) return;

                var wasOpen = !menu.hidden;

                // Tutup semua dropdown yang terbuka.
                document.querySelectorAll('.catcard__menu').forEach(function (m) { m.hidden = true; });
                document.querySelectorAll('.catcard__row.is-open').forEach(function (b) { b.classList.remove('is-open'); });

                if (wasOpen) return;

                btn.classList.add('is-open');
                menu.hidden = false;

                if (menu.dataset.loaded === '1') return;

                menu.innerHTML = '<div class="catcard__loading">Memuat&hellip;</div>';
                fetch(api + '/' + encodeURIComponent(btn.dataset.subUri), { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (j) {
                        var items = (j.articles && j.articles.data) || [];
                        if (!items.length) {
                            menu.innerHTML = '<div class="catcard__loading">Belum ada artikel.</div>';
                            return;
                        }
                        menu.innerHTML = items.map(function (a) {
                            return '<a href="' + articleBase + '/' + esc(a.uri) + '">' + esc(a.title) + '</a>';
                        }).join('');
                        menu.dataset.loaded = '1';
                    })
                    .catch(function () {
                        menu.innerHTML = '<div class="catcard__loading">Gagal memuat.</div>';
                    });
            });
        })();
        </script>
    @endonce
@endif
