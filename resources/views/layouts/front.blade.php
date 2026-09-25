<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="xvqI3mYc-iOX3n-dkgrBpm96bP3cmV4u5imTavVbSEQ" />
    <title>{{ $title ?? 'Beranda Hukum' }}</title>
    <meta name="description" content="{{ $og_description ?? 'Beranda Hukum — wadah belajar, menambah wawasan, dan berbagi tentang hukum.' }}" />
    <meta property="og:locale" content="id_ID" />
    <meta property="og:type" content="{{ $og_type ?? 'website' }}" />
    <meta property="og:title" content="{{ $og_title ?? ($title ?? 'Beranda Hukum') }}" />
    <meta property="og:description" content="{{ $og_description ?? '' }}" />
    <meta property="og:image" content="{{ base_url('assets/img/logo-share.jpeg') }}" />
    <meta property="og:url" content="{{ $og_url ?? url('/') }}" />
    <meta property="og:site_name" content="Berandahukum.com" />
    <meta name="twitter:card" content="{{ $twitter_type ?? 'summary_large_image' }}" />
    <meta name="twitter:title" content="{{ $og_title ?? ($title ?? 'Beranda Hukum') }}" />
    <meta name="twitter:description" content="{{ $og_description ?? '' }}" />
    <meta name="twitter:site" content="@berandahukum" />
    <link rel="icon" type="image/png" href="{{ base_url('favicon.png?v=0.0.1') }}" />
    <link rel="stylesheet" href="{{ base_url('assets/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ url("css/site.css?v=15") }}">
</head>
<body>
    <a class="skip-link" href="#content">Lewati ke konten</a>

    @include('partials.menu')

    <main id="content" class="page">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.popup', ['pop' => $frontPopup ?? null])
</body>
</html>
