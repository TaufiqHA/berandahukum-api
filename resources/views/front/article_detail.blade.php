@extends('layouts.front')

@section('content')
<div class="wrap">
    <div class="layout">
        <div class="main">
            <article class="article">
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

                @if (!empty($article['article_img']) && is_file(public_path('uploads/img/'.$article['article_img'])))
                    <figure class="article__figure">
                        <img src="{{ url('uploads/img/'.$article['article_img']) }}" alt="{{ $article['article_title'] }}">
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

            @if (!empty($related_post))
                <hr class="rule">
                <section class="section">
                    <div class="section__head"><h2>Artikel Terkait</h2></div>
                    <div class="grid-3">
                        @foreach ($related_post as $a)
                            <article class="card">
                                <a class="card__media" href="{{ site_url('a/'.$a['article_uri']) }}">
                                    <img src="{{ article_image($a['article_img'] ?? null) }}" alt="{{ $a['article_title'] }}" loading="lazy">
                                </a>
                                <div class="card__body">
                                    <h3 class="card__title" style="font-size:.95rem;"><a href="{{ site_url('a/'.$a['article_uri']) }}">{{ $a['article_title'] }}</a></h3>
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
                                <div class="comment__reply"><strong>Jawaban:</strong><br>{!! nl2br(e($c['comment_reply'])) !!}</div>
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
</script>
@endsection
