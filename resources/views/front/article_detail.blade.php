@extends('layouts.front')

@section('content')
<div class="wrap">
    <div class="layout layout--article">
        {{-- Header artikel: dipisah ke baris grid sendiri agar sidebar (kolom
             kanan) mulai sejajar dengan gambar artikel. --}}
        <div class="article-head">
            {{-- Iklan banner atas halaman artikel (posisi 9). --}}
            @include('partials.ad', ['ad' => $frontService->getArticleById(9)])

            @if (!empty($article['label_id']))
                @php $lbl = \App\Models\Label::find($article['label_id']); @endphp
                @if ($lbl)
                    <a class="kicker" href="{{ site_url('l/'.$lbl->label_uri) }}">{{ $lbl->label_name }}</a>
                @endif
            @endif
            <h1 class="article__title">{{ $article['article_title'] }}</h1>
            <div class="article__meta">
                <time datetime="{{ $article['article_date'] ?? '' }}">{{ format_tanggal($article['article_date'] ?? '', 'in') }}</time>
                <span>&middot;</span>
                <span>{{ !empty($article['article_author']) ? $article['article_author'] : $admin_name }}</span>
                <span>&middot;</span>
                <span>{{ (int) ($article['article_views'] ?? 0) }}x dibaca</span>
            </div>

            @php $shareUrl = urlencode(site_url('a/'.$article['article_uri'])); $shareTitle = urlencode($article['article_title']); @endphp
            <div class="card__meta" style="gap:var(--s3);margin-bottom:var(--s4);">
                <span class="muted small">Bagikan:</span>
                <a class="small" href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener">Twitter</a>
                <a class="small" href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener">Facebook</a>
                <a class="small" href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank" rel="noopener">WhatsApp</a>
                <a class="small" href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareTitle }}" target="_blank" rel="noopener">Telegram</a>
            </div>
        </div>

        <div class="main">
            <article class="article">
                @php
                    $articleImg = $article['article_img'] ?? '';
                    $hasArticleImage = ! empty($articleImg)
                        && ! in_array($articleImg, ['beranda-hukum.jpg', 'beranda_hukum_square.png'], true)
                        && is_file(public_path('uploads/img/'.$articleImg));
                @endphp
                @if ($hasArticleImage)
                    <figure class="article__figure">
                        <img src="{{ url('uploads/img/'.$articleImg) }}" alt="{{ $article['article_title'] }}">
                    </figure>
                @endif

                <div class="article__body">{!! $article['article_content'] !!}</div>

                @if (!empty($article['article_pdf']))
                    @php $pdf = str_starts_with($article['article_pdf'], 'http') ? $article['article_pdf'] : url('uploads/pdf/'.$article['article_pdf']); @endphp
                    <figure class="article__figure">
                        <iframe src="{{ $pdf }}" style="width:100%;height:700px;border:1px solid var(--n-200);" title="Dokumen PDF" loading="lazy"></iframe>
                    </figure>
                @endif

                @if (!empty($referensibacaan))
                    <hr class="rule">
                    <section class="section">
                        <div class="section__head"><h2>Referensi Bacaan</h2></div>
                        <ul>
                            @foreach ($referensibacaan as $r)
                                <li style="margin-bottom:8px;">
                                    @if (($r['sumber'] ?? '') === 'int')
                                        <a href="{{ site_url('a/'.$r['article_uri']) }}">{{ $r['article_title'] }}</a>
                                    @else
                                        <a href="{{ $r['link'] }}" target="_blank" rel="noopener">{{ $r['judul'] }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </article>

            {{-- Iklan bawah isi artikel, sebelum "Artikel Terkait" (posisi 10). --}}
            @include('partials.ad', ['ad' => $frontService->getArticleById(10)])

            @if (!empty($related_post))
                <hr class="rule">
                <section class="section">
                    <div class="section__head"><h2>Artikel Terkait</h2></div>
                    <div class="grid-3">
                        @foreach ($related_post as $a)
                            <article class="card">
                                <div class="card__body">
                                    <h3 class="card__title" style="font-size:.95rem;"><a href="{{ site_url('a/'.$a['article_uri']) }}">{{ $a['article_title'] }}</a></h3>
                                    <div class="card__meta">
                                        <time datetime="{{ $a['article_date'] ?? '' }}">{{ format_tanggal($a['article_date'] ?? '', 'in') }}</time>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <hr class="rule">
            <section class="section">
                <div class="section__head"><h2>Komentar</h2></div>
                <ul class="comments">
                    @forelse ($comments as $c)
                        <li class="comment">
                            <p><span class="comment__author">{{ $c['comment_name'] }}</span> <span class="comment__time">{{ $c['comment_date'] }}</span></p>
                            <p style="margin:0;">{!! nl2br(e($c['comment_fill'])) !!}</p>
                            @if (!empty($c['comment_reply']))
                                <div class="comment__reply"><strong>Jawaban:</strong><br>{!! render_content($c['comment_reply']) !!}</div>
                            @endif
                        </li>
                    @empty
                        <li class="empty">Belum ada komentar. Jadilah yang pertama.</li>
                    @endforelse
                </ul>

                <div id="msg_error"></div>
                <form id="form-comment" style="margin-top:var(--s5);max-width:560px;">
                    @csrf
                    <input type="hidden" name="article_id" value="{{ $article['article_id'] }}">
                    <div class="field">
                        <label for="comment_nama">Nama</label>
                        <input type="text" id="comment_nama" name="comment_nama" required>
                    </div>
                    <div class="field">
                        <label for="comment_fill">Komentar</label>
                        <textarea id="comment_fill" name="comment_fill" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn--primary">Kirim Komentar</button>
                </form>
            </section>
        </div>

        @include('partials.side')
    </div>
</div>

<script>
    document.getElementById('form-comment')?.addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this;
        fetch("{{ site_url('add_comment') }}", { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(function (res) {
                document.getElementById('msg_error').innerHTML = res.message || '';
                form.reset();
            });
    });

    // Pencarian di header mencari kata di dalam artikel yang sedang dibuka.
    (function () {
        var form = document.querySelector('header.site-header form.search[data-article-search]');
        var input = document.getElementById('q');
        var body = document.querySelector('.article__body');
        if (!form || !input || !body) {
            return;
        }

        var hits = [];
        var hitIndex = -1;
        var activeTerm = '';

        var clearHits = function () {
            body.querySelectorAll('mark.find-hit').forEach(function (mark) {
                mark.parentNode.replaceChild(document.createTextNode(mark.textContent), mark);
            });
            body.normalize();
            hits = [];
            hitIndex = -1;
            activeTerm = '';
        };

        var showHit = function (index) {
            if (!hits.length) {
                return;
            }
            hits.forEach(function (hit) { hit.classList.remove('is-current'); });
            hitIndex = (index + hits.length) % hits.length;
            var hit = hits[hitIndex];
            hit.classList.add('is-current');
            hit.scrollIntoView({ behavior: 'smooth', block: 'center' });
        };

        var escapeRegExp = function (term) {
            return term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        };

        var highlight = function (term) {
            var regex = new RegExp(escapeRegExp(term), 'gi');
            var nodes = [];
            var walker = document.createTreeWalker(body, NodeFilter.SHOW_TEXT, {
                acceptNode: function (node) {
                    if (!node.nodeValue.trim()) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    if (node.parentElement && node.parentElement.closest('script, style')) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    regex.lastIndex = 0;
                    return regex.test(node.nodeValue) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
                }
            });

            while (walker.nextNode()) {
                nodes.push(walker.currentNode);
            }

            nodes.forEach(function (node) {
                var text = node.nodeValue;
                var fragment = document.createDocumentFragment();
                var last = 0;
                var match;
                regex.lastIndex = 0;
                while ((match = regex.exec(text)) !== null) {
                    if (match[0].length === 0) {
                        regex.lastIndex++;
                        continue;
                    }
                    fragment.appendChild(document.createTextNode(text.slice(last, match.index)));
                    var mark = document.createElement('mark');
                    mark.className = 'find-hit';
                    mark.textContent = match[0];
                    fragment.appendChild(mark);
                    hits.push(mark);
                    last = match.index + match[0].length;
                }
                fragment.appendChild(document.createTextNode(text.slice(last)));
                node.parentNode.replaceChild(fragment, node);
            });
        };

        form.addEventListener('submit', function (e) {
            var term = input.value.trim();
            if (!term) {
                e.preventDefault();
                clearHits();
                return;
            }

            e.preventDefault();
            input.setCustomValidity('');

            if (term !== activeTerm || !hits.length) {
                clearHits();
                activeTerm = term;
                highlight(term);

                if (!hits.length) {
                    input.setCustomValidity('Kata tidak ditemukan di artikel ini.');
                    input.reportValidity();
                    activeTerm = '';
                    return;
                }

                showHit(0);
                return;
            }

            showHit(hitIndex + 1);
        });

        input.addEventListener('input', function () {
            input.setCustomValidity('');
            if (!input.value.trim() && activeTerm) {
                clearHits();
            }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                clearHits();
                input.setCustomValidity('');
            }
        });
    })();
</script>
@endsection
