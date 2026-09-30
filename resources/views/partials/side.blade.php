{{--
    Sidebar halaman non-beranda — dibuat sama persis dengan sidebar beranda
    (resources/views/legacy/side.blade.php): iklan posisi 8,17,18,19,20, tombol
    "Kirim Pertanyaan", lalu akordeon kategori → sub-kategori → artikel.

    Berbeda dari beranda, halaman ini memakai layouts.front yang hanya memuat
    site.css (tanpa Bootstrap/jQuery), jadi gaya kartu Bootstrap disediakan
    ulang di bawah (di-scope ke .side) dan skrip akordeon memakai vanilla JS.
--}}
<aside class="side">
    <div id="side-menu">
        @foreach ([8, 17, 18, 19, 20] as $pos)
            @php
                $nama = [8 => 'delapan', 17 => '17', 18 => '18', 19 => '19', 20 => '20'][$pos];
                $embedHeight = in_array($pos, [17, 18, 19, 20], true) ? '50px' : '';
            @endphp
            @include('legacy.ad', [
                'ad' => $frontService->getArticleById($pos),
                'imgClass' => 'img-fluid',
                'idShow' => 'show_iklan_'.$nama,
                'embedHeight' => $embedHeight,
            ])
        @endforeach

        @if (($frontSys['show_pertanyaan'] ?? null) === 'yes')
        <p style="display:block;text-align:center;">
            <a href="{{ site_url('kirimpertanyaan') }}" class="button-pertanyaan" style="margin: 5px auto;">Kirim Pertanyaan</a>
        </p>
        @endif

        <style>
        .card-body a, a:active{ color: #55504F; font-weight:600; }

        /* Akordeon kategori & sub-kategori pada sidebar. */
        #side-menu .acc-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin: 0;
            background: none;
            border: 0;
            text-align: left;
            color: inherit;
            font: inherit;
            cursor: pointer;
        }
        #side-menu .acc-head:focus-visible {
            outline: 2px solid #FF4F4F;
            outline-offset: -2px;
        }
        #side-menu .acc-chev {
            flex: 0 0 auto;
            color: #FF4F4F;
            font-size: 12px;
            line-height: 1;
            transition: transform .2s ease;
        }
        #side-menu .acc-head[aria-expanded="true"] .acc-chev { transform: rotate(180deg); }
        #side-menu .card-body { padding: 12px 14px; }
        #side-menu .sub-acc { border-top: 1px solid #eee; }
        #side-menu .sub-acc:first-child { border-top: 0; }
        #side-menu .sub-acc__head { padding: 10px 0; font-weight: 600; }
        #side-menu .sub-acc__head[aria-expanded="true"] { color: #FF4F4F; }
        #side-menu .sub-acc__body {
            border-left: 2px solid #f0f0f0;
            margin: 2px 0 12px 2px;
            padding-left: 10px;
        }
        #side-menu .sub-acc__body ul,
        #side-menu .acc-list { padding-inline-start: 0; margin: 0; }
        #side-menu .sub-acc__body li,
        #side-menu .acc-list li { list-style: none; margin: 0; }
        #side-menu .sub-acc__body a,
        #side-menu .acc-list a {
            display: block;
            padding: 6px 8px;
            line-height: 1.45;
            border-radius: 2px;
        }
        #side-menu .sub-acc__body a:hover,
        #side-menu .acc-list a:hover { background: #f5f5f5; color: #FF4F4F; }
        #side-menu .acc-empty { margin: 0 0 12px; color: #6c757d; }

        /* Gaya kartu (padanan Bootstrap) — hanya untuk halaman non-beranda
           yang tidak memuat Bootstrap. Di beranda, aturan ini tidak terpakai. */
        .side #side-menu { font-size: 14px; }
        .side #side-menu .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: .25rem;
        }
        .side #side-menu .mb-3 { margin-bottom: 1rem !important; }
        .side #side-menu .card .card-header {
            padding: .75rem 1.25rem;
            margin-bottom: 0;
            background-color: rgba(0, 0, 0, .03);
            border-bottom: 3px solid #FF4F4F;
            border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
        }
        .side #side-menu .card-body { flex: 1 1 auto; min-height: 1px; }
        .side #side-menu .img-fluid { max-width: 100%; height: auto; }
        .side #side-menu .embed-responsive { position: relative; display: block; width: 100%; padding: 0; overflow: hidden; }
        .side #side-menu .embed-responsive-item { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
        .side #side-menu .button-pertanyaan {
            background-color: #FF0000 !important;
            border: none !important;
            border-radius: 1px !important;
            box-shadow: none !important;
            color: #fff !important;
            cursor: pointer;
            font-family: 'Open Sans', Arial, Helvetica, sans-serif !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            line-height: 21px !important;
            height: auto;
            padding: 10px !important;
            width: 98% !important;
            display: block;
            box-sizing: border-box !important;
        }
        </style>

        @foreach ($frontService->categories() as $c)
            @php
                $category_id = $c['category_id'];
                $sc_count = $c['sc_count'];
            @endphp

            @if (($c['category_show'] ?? 'no') === 'yes')
                <div class="card mb-3 cat-acc">
                    <button type="button" class="card-header acc-head cat-acc__head" aria-expanded="false">
                        <span>{{ $c['category_name'] }}</span>
                        <span class="acc-chev" aria-hidden="true">&#9662;</span>
                    </button>
                    <div class="card-body cat-acc__body" hidden>
                        @if ($sc_count > 0)
                            @foreach ($frontService->sideSubCategories((int) $category_id) as $sc)
                                @if (($sc['sub_category_show'] ?? 'no') === 'yes')
                                    @php $sub_category_id = $sc['sub_category_id']; @endphp
                                    <div class="sub-acc">
                                        <button type="button" class="acc-head sub-acc__head" aria-expanded="false">
                                            <span>{{ $sc['sub_category_name'] }}</span>
                                            <span class="acc-chev" aria-hidden="true">&#9662;</span>
                                        </button>
                                        <div class="sub-acc__body" hidden>
                                            @php $subArticles = $frontService->sideSubCategoryArticles((int) $category_id, (int) $sub_category_id); @endphp
                                            @if (! empty($subArticles))
                                                <ul>
                                                    @foreach ($subArticles as $a)
                                                        <li><a href="{{ site_url('a/'.$a['article_uri']) }}" target="_blank">{{ $a['article_title'] }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="acc-empty">Belum ada artikel.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            @php $article = $frontService->sideCategoryArticles((int) $category_id); @endphp
                            @if (! empty($article))
                                <ul class="acc-list">
                                    @foreach ($article as $a)
                                        <li><a href="{{ site_url('a/'.$a['article_uri']) }}" target="_blank">{{ $a['article_title'] }}</a></li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="acc-empty">Belum ada artikel.</p>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        @endforeach

        @once
        <script>
        (function () {
            var menu = document.getElementById('side-menu');
            if (!menu) return;

            // Tingkat kategori: satu kategori terbuka dalam satu waktu.
            menu.querySelectorAll('.cat-acc__head').forEach(function (head) {
                head.addEventListener('click', function () {
                    var open = head.getAttribute('aria-expanded') === 'true';

                    menu.querySelectorAll('.cat-acc__head').forEach(function (h) {
                        if (h !== head) h.setAttribute('aria-expanded', 'false');
                    });
                    menu.querySelectorAll('.cat-acc').forEach(function (card) {
                        if (card.querySelector(':scope > .cat-acc__head') !== head) {
                            var b = card.querySelector(':scope > .cat-acc__body');
                            if (b) b.hidden = true;
                        }
                    });

                    head.setAttribute('aria-expanded', open ? 'false' : 'true');
                    var body = head.parentElement ? head.parentElement.querySelector(':scope > .cat-acc__body') : null;
                    if (body) body.hidden = open;
                });
            });

            // Tingkat sub-kategori: buka/tutup daftar artikelnya.
            menu.querySelectorAll('.sub-acc__head').forEach(function (head) {
                head.addEventListener('click', function () {
                    var open = head.getAttribute('aria-expanded') === 'true';

                    menu.querySelectorAll('.sub-acc__head').forEach(function (h) {
                        if (h !== head) h.setAttribute('aria-expanded', 'false');
                    });
                    menu.querySelectorAll('.sub-acc').forEach(function (sub) {
                        if (sub.querySelector(':scope > .sub-acc__head') !== head) {
                            var b = sub.querySelector(':scope > .sub-acc__body');
                            if (b) b.hidden = true;
                        }
                    });

                    head.setAttribute('aria-expanded', open ? 'false' : 'true');
                    var body = head.parentElement ? head.parentElement.querySelector(':scope > .sub-acc__body') : null;
                    if (body) body.hidden = open;
                });
            });
        })();
        </script>
        @endonce
    </div>
</aside>
