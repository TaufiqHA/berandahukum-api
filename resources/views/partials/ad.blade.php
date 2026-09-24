@php
    $ad = $ad ?? null;
    $image = $article_image ?? null;
    $href = ($ad['ads_link'] ?? '');
    $href = ($href === '' || $href === '#') ? '#' : (str_starts_with($href, 'http') ? $href : 'http://'.$href);
    $external = ($href !== '#');
@endphp
@if (!empty($ad))
    @php
        $src = (($ad['ads_type'] ?? '0') == '0') ? url('uploads/i/'.$ad['ads_url']) : $ad['ads_url'];
    @endphp
    <div class="ad">
        @if (($ad['ads_file_type'] ?? '0') == '0')
            <a @if ($external) target="_blank" rel="noopener" @endif href="{{ $href }}">
                <img src="{{ $src }}" alt="{{ $ad['ads_link'] ? 'Iklan' : 'Iklan' }}" loading="lazy">
            </a>
        @elseif (($ad['ads_file_type'] ?? '0') == '2')
            {!! $src !!}
        @else
            <a @if ($external) target="_blank" rel="noopener" @endif href="{{ $href }}" aria-label="Iklan">
                <iframe src="{{ $src }}" style="width:100%;aspect-ratio:16/9;border:0;" title="Iklan" loading="lazy"></iframe>
            </a>
        @endif
    </div>
@endif
