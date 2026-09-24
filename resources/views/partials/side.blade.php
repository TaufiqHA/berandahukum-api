<aside class="side">
    @if (($frontSys['show_pertanyaan'] ?? null) === 'yes')
        <p><a class="btn btn--primary btn--block" href="{{ site_url('kirimpertanyaan') }}">Kirim Pertanyaan</a></p>
    @endif

    @foreach ([8, 9, 10, 16, 17, 18, 19, 20] as $pos)
        @include('partials.ad', ['ad' => $frontService->getArticleById($pos)])
    @endforeach

    @if (!empty($frontCategoriesTree))
        <div class="widget">
            <h2>Kategori</h2>
            <ul>
                @foreach ($frontCategoriesTree as $cat)
                    <li>
                        <a href="{{ site_url('k/'.$cat['uri']) }}">{{ $cat['name'] }}</a>
                        @if (!empty($cat['subs']))
                            <ul style="margin-top:6px;">
                                @foreach ($cat['subs'] as $sub)
                                    <li><a href="{{ site_url('s/'.$sub['uri']) }}" style="color:var(--n-500);font-size:.85rem;">{{ $sub['name'] }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</aside>
