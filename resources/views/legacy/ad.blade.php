{{--
    Partial iklan gaya lama (padanan getArticleById + blok iklan pada
    welcome_view_new.php / side_front_view.php / menu_front_view.php /
    footer_front_view.php / bottom-front-view.php).

    Parameter:
      $ad       data iklan (array) — biasanya $frontService->getArticleById($pos)
      $imgClass class <img> (mis. image-top-of-headline, image-iklan-empat)
      $idShow   id untuk wadah iklan tipe script (file_type = 2)
      $addHttp  true bila tautan tanpa skema harus diberi awalan http://
      $width    lebar gambar (default 100%)
      $embedHeight tinggi tetap wadah embed (mis. '50px'), kosong = rasio 16:9
      $aClass   class tambahan untuk <a> (mis. ads-top-menu)
--}}
@php
    $ad = $ad ?? null;
    $imgClass = $imgClass ?? 'img-fluid';
    $idShow = $idShow ?? 'show_iklan';
    $addHttp = $addHttp ?? false;
    $width = $width ?? '100%';
    $embedHeight = $embedHeight ?? '';
    $aClass = $aClass ?? '';

    $link = '';
    $href = '#';
    $external = false;

    if (! empty($ad)) {
        $link = (($ad['ads_type'] ?? '0') == '0')
            ? base_url('uploads/i/'.$ad['ads_url'])
            : ($ad['ads_url'] ?? '');

        $adsLink = $ad['ads_link'] ?? '';
        $external = ($adsLink !== '' && $adsLink !== '#');
        if ($external) {
            if ($addHttp && ! preg_match('~^https?://~i', $adsLink)) {
                $adsLink = 'http://'.$adsLink;
            }
            $href = $adsLink;
        }
    }
@endphp
@if (! empty($ad))
    @if (($ad['ads_file_type'] ?? '0') == '0')
        <a @if ($aClass !== '') class="{{ $aClass }}" @endif @if ($external) target="_blank" @endif href="{{ $href }}">
            <img style="margin-bottom: 10px;width: {{ $width }};" src="{{ $link }}" class="{{ $imgClass }}" alt="Responsive image">
        </a>
    @elseif (($ad['ads_file_type'] ?? '0') == '2')
        <div id="{{ $idShow }}"{!! $idShow === 'show_iklan_delapan' ? ' style="margin-bottom: 10px;"' : '' !!}>
            {!! $link !!}
        </div>
    @else
        <div class="embed-responsive embed-responsive-16by9" style="margin-bottom: 10px;{{ $embedHeight !== '' ? 'height:'.$embedHeight.';' : '' }}border-radius: 4px;">
            <iframe class="embed-responsive-item" src="{{ $link }}" allowfullscreen></iframe>
        </div>
    @endif
@endif
