<div class="row">
    <div class="col-md-12">
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

            /* Akordeon kategori & sub-kategori pada sidebar beranda (desktop). */
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
            jQuery(function ($) {
                var $menu = $('#side-menu');

                // Tingkat kategori: satu kategori terbuka dalam satu waktu.
                $menu.on('click', '.cat-acc__head', function () {
                    var open = $(this).attr('aria-expanded') === 'true';
                    $menu.find('.cat-acc__head').not(this).attr('aria-expanded', 'false');
                    $menu.find('.cat-acc').not($(this).closest('.cat-acc')).children('.cat-acc__body').attr('hidden', true);
                    $(this).attr('aria-expanded', open ? 'false' : 'true');
                    $(this).next('.cat-acc__body').attr('hidden', open);
                });

                // Tingkat sub-kategori: buka/tutup daftar artikelnya.
                $menu.on('click', '.sub-acc__head', function () {
                    var open = $(this).attr('aria-expanded') === 'true';
                    $menu.find('.sub-acc__head').not(this).attr('aria-expanded', 'false');
                    $menu.find('.sub-acc').not($(this).closest('.sub-acc')).children('.sub-acc__body').attr('hidden', true);
                    $(this).attr('aria-expanded', open ? 'false' : 'true');
                    $(this).next('.sub-acc__body').attr('hidden', open);
                });
            });
            </script>
            @endonce
        </div>
    </div>
</div>
