@extends('layouts.home_legacy')

@section('content')
<style type="text/css">
    .carousel-item {
      min-height:400px;
      background: no-repeat center center scroll;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
    }
    .headline-text:hover{
        color: #ffc107;
    }
    .article-text:hover{
        color: #007bff;
    }

</style>
<style>
	
.callbacks_container {
  margin-bottom: 10px;
  position: relative;
  float: left;
  width: 100%;
  }

.callbacks {
  position: relative;
  list-style: none;
  overflow: hidden;
  width: 100%;
  padding: 0;
  margin: 0;
  }

.callbacks li {
  position: absolute;
  width: 100%;
  left: 0;
  top: 0;
  }

.callbacks img {
  display: block;
  position: relative;
  z-index: 1;
  height: auto;
  width: 100%;
  border: 0;
  }

.callbacks .caption {
  display: block;
  /*position: absolute;*/
  z-index: 2;
  font-size: 20px;
  font-weight : 600;
  line-height : 21px;
  text-shadow: none;
  background-color: #0072c6;
  color: #fff;
  left: 0;
  right: 0;
  bottom: 0;
  padding: 10px 10px;
  margin: 0;
  max-width: none;
  }

.callbacks_nav {
  position: absolute;
  -webkit-tap-highlight-color: rgba(0,0,0,0);
  top: 52%;
  left: 0;
  opacity: 0.7;
  z-index: 3;
  text-indent: -9999px;
  overflow: hidden;
  text-decoration: none;
  height: 61px;
  width: 38px;
  background: transparent url("assets/themes.gif") no-repeat left top;
  margin-top: -45px;
  }

.callbacks_nav:active {
  opacity: 1.0;
  }

.callbacks_nav.next {
  left: auto;
  background-position: right top;
  right: 0;
 }


@media only screen and  (max-width: 460px) {	
	.callbacks .caption { font-size: 18px !Important; }
	#artpilihan { display: none !Important; }
	#artpilihanbawah { display: none !Important; }
}	
@media only screen and  (max-width: 360px) {	
	.callbacks .caption { font-size: 18px !Important; }
	#artpilihan { display: none !Important; }
	#artpilihanbawah { display: none !Important; }
}	


</style>

@include('legacy.ad', ['ad' => $frontService->getArticleById(2), 'imgClass' => 'image-top-of-headline', 'idShow' => 'show_iklan_dua', 'addHttp' => true])

@if (count($tmpHeadLine ?? []) > 0)
<header>
	<div class="callbacks_container">
    <ul class="rslides" id="slider4">
         @foreach ($tmpHeadLine as $h)
		<li>
		<a href="{{ site_url('a/'.$h['article_uri']) }}"><img src="{{ article_image($h['article_img'] ?? null) }}" alt="">
		<div style="clear:both;"></div>
		<p class="caption">{{ $h['article_title'] }}<br /><span style="font-size: 12px;">{{ $h['article_author'] }}</span></p>
		</a>
		</li>
		@endforeach
	</ul></div>
	   
</header>
@endif
<!--ARTIKEL PILIHAN -->
<hr class="d-lg-flex">
<!--BANER HOME -->
@if (! empty($banner_home))
<div class="row">
	
	<div class="col-md-12 owl-carousel owl-theme" id="banner_home" >
	@foreach ($banner_home as $a)
	@php
        $btype = (int) ($a['banner_type'] ?? 0);
        $banner_img = 'uploads/img/'.($a['file_banner'] ?? '');
        $hasBannerImg = ! empty($a['file_banner']) && is_file(public_path($banner_img));
    @endphp
	@if ($btype === 2 && ! empty($a['banner_content']))
	<div class="item" style="margin-bottom: 10px;" >
		{!! $a['banner_content'] !!}
    </div>
	@elseif ($btype === 1 && ! empty($a['banner_content']))
	<div class="item" style="margin-bottom: 10px;" >
		<div class="blog-first clearfix">
                    <div class="embed-responsive embed-responsive-16by9" style="border-radius: 4px;">
                        <iframe class="embed-responsive-item" src="{{ $a['banner_content'] }}" allowfullscreen></iframe>
                    </div>
       </div>
    </div>
	@elseif ($hasBannerImg)
	<div class="item" style="margin-bottom: 10px;" >
		<div class="blog-first clearfix">
                    <div >
                        <a href="{{ $a['link_url'] }}" target="_blank"><img class="image-ap" src="{{ base_url($banner_img) }}"   alt=""></a>
                    </div>
                    
       </div>
    </div>
	@endif
    @endforeach
    </div>
</div>
@endif
<!--END BANNER-->
@if (! empty($article_pilihanatas))
<div class="row">
	
	<div class="col-md-12 owl-carousel owl-theme" id="artpilihan" >
	@foreach ($article_pilihanatas as $a)
	@php
        $article_img = article_image($a['article_img'] ?? null);
        $url_first = site_url('a/'.$a['article_uri']);
    @endphp
	<div class="item" style="margin-bottom: 10px;" >
		<div class="blog-first clearfix">
                    <div >
                        <a href="{{ $url_first }}"><img class="image-ap" src="{{ $article_img }}"   alt=""></a>
                    </div>
                    <div style="clear:both;"></div>
                    <div style="margin-top:5px;">
                        <a href="{{ $url_first }}"><h2 style="color: #000;font-size: 14px;font-weight: 600;">{{ $a['article_title'] }}</h2></a>
                    </div>
                    @if (($a['article_author'] ?? '') != '')
                    <p style="font-size:11px;">Oleh : {{ $a['article_author'] }}</p>
                    @endif
       </div>
        
    </div>
    
    @endforeach
    </div>
</div>
@endif

@if (! empty($article_pilihanbawah))
<div class="row">
	
	<div class="col-md-12 owl-carousel owl-theme" id="artpilihanbawah" >
	@foreach ($article_pilihanbawah as $a)
	@php
        $article_img = article_image($a['article_img'] ?? null);
        $url_first = site_url('a/'.$a['article_uri']);
    @endphp
	<div class="item" style="margin-bottom: 10px;" >
		<div class="blog-first clearfix">
                    <div >
                        <a href="{{ $url_first }}"><img class="image-ap" src="{{ $article_img }}" alt=""></a>
                    </div>
                    <div style="clear:both;"></div>
                    <div style="margin-top:5px;">
                        <a href="{{ $url_first }}"><h2 style="color: #000;font-size: 14px;font-weight: 600;">{{ $a['article_title'] }}</h2></a>
                    </div>
                    @if (($a['article_author'] ?? '') != '')
                    <p style="font-size:11px;">Oleh : {{ $a['article_author'] }}</p>
                    @endif
       </div>
        
    </div>
    
    @endforeach
    </div>
</div>
@endif

                      
 <script type="text/javascript">

                            jQuery(document).ready(function($) {
								
								var ocImagesBanner = $("#banner_home");

                                ocImagesBanner.owlCarousel({
                                    responsive:{
                                        0:{ items:1 },
                                        480:{ items:2 },
                                        768:{ items:3 },
                                        992:{ items:3 },
                                        1200:{ items:3 }
                                    },
                                    margin: 20,
									autoplay : true,
									autoplayTimeout : 3000,
									rewind : true,
                                    nav: false,
                                    //navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                                    rewindNav: true,
                                    dots: true
                                });

                                var ocImages2 = $("#artpilihan");

                                ocImages2.owlCarousel({
                                    responsive:{
                                        0:{ items:1 },
                                        480:{ items:2 },
                                        768:{ items:3 },
                                        992:{ items:3 },
                                        1200:{ items:3 }
                                    },
                                    margin: 20,
                                    nav: true,
                                    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                                    rewindNav: true,
                                    dots: false
                                });
                                
                                var ocImages3 = $("#artpilihanbawah");

                                ocImages3.owlCarousel({
                                    responsive:{
                                        0:{ items:1 },
                                        480:{ items:2 },
                                        768:{ items:3 },
                                        992:{ items:3 },
                                        1200:{ items:3 }
                                    },
                                    margin: 20,
                                    nav: true,
                                    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                                    rewindNav: true,
                                    dots: false
                                });

                            });
	 
	 						
</script>
<!--END ARTIKEL PILIHAN -->
<hr>
<div class="row">
    <div class="col-md-6">
        @include('legacy.ad', ['ad' => $frontService->getArticleById(3), 'imgClass' => 'image-iklan-empat', 'idShow' => 'show_iklan_tiga', 'addHttp' => true])
    </div>
    <div class="col-md-6">
        @include('legacy.ad', ['ad' => $frontService->getArticleById(4), 'imgClass' => 'image-iklan-empat', 'idShow' => 'show_iklan_empat', 'addHttp' => true])
    </div>
</div>
<hr class="d-none d-lg-flex">
<div class="row d-none d-lg-flex">
    @foreach ($articleDesktop as $a)
    @php $article_img = article_image($a['article_img'] ?? null); @endphp
    <div class="col-6 col-md-6 col-lg-4" style="margin-bottom: 10px;">
        <div class="card h-100 mb-3" style="border: none;">
            <div class="img-hover-zoom" style="border-radius: 4px;margin-bottom: 15px;">
                <img style="height: 145px;" src="{{ $article_img }}" class="card-img-top" alt="image">
            </div>
          <div class="card-body" style="padding: 0;">
            <a class="card-title text-dark" href="{{ site_url('a/'.$a['article_uri']) }}"><h6 class="article-text" style="font-size: 18px;font-weight: 600;">{{ short_name(strip_tags($a['article_title']), 70) }}</h6></a>
          </div>
        </div>
    </div>
    @endforeach
</div>

<div id="divMiddle"></div>

<div class="row">
    <div class="col-md-6">
        @include('legacy.ad', ['ad' => $frontService->getArticleById(5), 'imgClass' => 'image-iklan-empat', 'idShow' => 'show_iklan_lima', 'addHttp' => true])
    </div>
    <div class="col-md-6">
        @include('legacy.ad', ['ad' => $frontService->getArticleById(6), 'imgClass' => 'image-iklan-empat', 'idShow' => 'show_iklan_enam', 'addHttp' => true])
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @include('legacy.ad', ['ad' => $frontService->getArticleById(14), 'imgClass' => 'image-iklan-tujuh', 'idShow' => 'show_iklan_empatbelas', 'addHttp' => true])
    </div>
</div>

<div class="divLabel">
    <div class="row d-none d-lg-flex">
    <div class="row" @if (count($label) == 1) style="display:block;width: 100%;" @endif>
    @foreach ($label as $l)
        @if (($l['jumlah'] ?? 0) > 0)
            @php
                $label_id = $l['label_id'];
                $urlLabel = site_url("l/".$l['label_uri']);
            @endphp
            <div class="col-md-6">
                <div class="jumbotron">
                    <div class="div-post-title">
                        <a href="{{ $urlLabel }}"><h2>{{ $l['label_name'] }} <span class="fa fa-arrow-circle-o-right"></span></h2></a>
                    </div>
                    @php
                        $detail = $frontService->articlesByLabel((int) $label_id, 5);
                    @endphp
                    @if (count($detail) > 0)
                        @php
                            $detail_first = $detail[0];
                            $url_first = site_url('a/'.$detail_first['article_uri']);
                            $article_img = article_image($detail_first['article_img'] ?? null);
                        @endphp

                        <div class="blog-first">
                            <div class="img-hover-zoom">
                                <img class="" src="{{ $article_img }}" alt="">
                            </div>
                            <div style="clear:both;"></div>
                            <div style="margin-top:5px;">
                                <a href="{{ $url_first }}"><h2 style="font-size: 18px;font-weight: 600;">{{ $detail_first['article_title'] }}</h2></a>
                            </div>
                            @if (($detail_first['article_author'] ?? '') != '')
                                <p>Oleh : {{ $detail_first['article_author'] }}</p>
                            @endif
                        </div>
                        @if (count($detail) > 1)
                            @foreach ($detail as $baris => $d)
                                @if ($baris > 0)
                                    @php
                                        $url_link = site_url('a/'.$d['article_uri']);
                                        $d_img = article_image($d['article_img'] ?? null);
                                    @endphp

                                    <div class="blog-child">
                                        <div class="row">
                                            <div class="col-4 col-img">
                                                <img style="height: 70px;" src="{{ $d_img }}" />
                                            </div>
                                            <div class="col-8">
                                                <div >
                                                    <a href="{{ $url_link }}"><h5 style="font-size: 14px;font-weight: 600;">{{ short_name(strip_tags($d['article_title']), 50) }}</h5></a>
                                                </div>
                                                <div class="item-meta">
                                                        @if (($d['article_author'] ?? '') != '')
                                                            <p>Oleh: {{ $d['article_author'] }}</p>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endif
            </div>
        </div>
        @endif
    @endforeach
    </div>
    </div>
</div>
<div class="row d-none d-lg-flex">
    <div class="col-md-12">
        <h5 style="font-weight: 600;">Most View Article</h5>
    </div>
    <div class="col-md-12">
        <hr>
    </div>
    <style>
		p.related-post-text { margin-top : 0.5em  !Important; margin-bottom:0.5em !Important;}
		@media only screen and  (max-width: 460px) {
			.list-group-item:first-child {  border-top: 1px solid rgba(0,0,0,.125) !Important;}
		}
		@media only screen and  (max-width: 360px) {
			.list-group-item:first-child {  border-top: 1px solid rgba(0,0,0,.125) !Important;}
		}
    </style>
    @php $most_view_articles = array_chunk($frontService->mostView(), 3); @endphp
    @foreach ($most_view_articles as $post)
    <div class="col-md-6">
        <ul class="list-group list-group-flush">
          <li class="list-group-item" style="font-weight: 600; padding:5px 0px; font-size: 13px;color: #000;">
                <a class="card-title text-dark" style="padding:0px 0px;margin-bottom:0px;" href="{{ site_url('a/'.$post[0]['article_uri']) }}">
                    <p class="related-post-text" >
                       {{ short_name(strip_tags($post[0]['article_title']), 90) }}
                    </p>
                </a>
              </li>
        @if (isset($post[1]))
          <li class="list-group-item" style="font-weight: 600; padding:5px 0px;font-size: 13px;color: #000;">
                <a class="card-title text-dark"  style="padding:0px 0px;margin-bottom:0px;" href="{{ site_url('a/'.$post[1]['article_uri']) }}">
                  <p class="related-post-text">
                      {{ short_name(strip_tags($post[1]['article_title']), 90) }}
                  </p>
                </a>
          </li>
        @endif
        @if (isset($post[2]))
          <li class="list-group-item" style="font-weight: 600; padding:5px 0px;font-size: 13px;color: #000;">
                <a class="card-title text-dark"  style="padding:0px 0px;margin-bottom:0px;" href="{{ site_url('a/'.$post[2]['article_uri']) }}">
                  <p class="related-post-text">
                      {{ short_name(strip_tags($post[2]['article_title']), 90) }}
                  </p>
              </a>
          </li>
        @endif
        </ul>
    </div>
    @endforeach
</div>
<div class="row d-none d-lg-flex">
    <div class="com-md-12">
        <hr>
    </div>
</div>
<div class="row d-none d-lg-flex">
    <div class="col-md-12">
        <h5 style="font-weight: 600;">Most Comment Article</h5>
    </div>
    <div class="col-md-12">
        <hr>
    </div>
    @php $most_comment_articles = array_chunk($frontService->mostComment(), 3); @endphp
    @foreach ($most_comment_articles as $post)
    <div class="col-md-6">
        <ul class="list-group list-group-flush">
          <li class="list-group-item" style="font-weight: 600;padding:5px 0px; font-size: 13px;color: #000;">
                <a class="card-title text-dark" style="padding:0px 0px;margin-bottom:0px;" href="{{ site_url('a/'.$post[0]['article_uri']) }}">
                    <p class="related-post-text">
                        {{ short_name(strip_tags($post[0]['article_title']), 90) }}
                    </p>
                </a>
              </li>
        @if (isset($post[1]))
          <li class="list-group-item" style="font-weight: 600;padding:5px 0; font-size: 13px;color: #000;">
                <a class="card-title text-dark" style="padding:0px 0px;margin-bottom:0px;" href="{{ site_url('a/'.$post[1]['article_uri']) }}">
                  <p class="related-post-text">
                      {{ short_name(strip_tags($post[1]['article_title']), 90) }}
                  </p>
                </a>
          </li>
        @endif
        @if (isset($post[2]))
          <li class="list-group-item" style="font-weight: 600;padding:5px 0; font-size: 13px;color: #000;">
                <a class="card-title text-dark" style="padding:0px 0px;margin-bottom:0px;" href="{{ site_url('a/'.$post[2]['article_uri']) }}">
                  <p class="related-post-text">
                      {{ short_name(strip_tags($post[2]['article_title']), 90) }}
                  </p>
              </a>
          </li>
        @endif
        </ul>
    </div>
    @endforeach
</div>
<div class="callbacks_container">
    <ul class="rslides" id="sliderquotes">		
        @foreach ($frontService->quotes() as $q)
            <li><img src="{{ base_url('uploads/img/'.$q['quote_image']) }}" alt=""></li>
        @endforeach
	</ul>
</div>

@include('legacy.ad', ['ad' => $frontService->getArticleById(7), 'imgClass' => 'image-iklan-tujuh', 'idShow' => 'show_iklan_tujuh', 'addHttp' => true])

@include('legacy.ad', ['ad' => $frontService->getArticleById(21), 'imgClass' => 'image-iklan-21', 'idShow' => 'show_iklan_21'])

@include('legacy.ad', ['ad' => $frontService->getArticleById(22), 'imgClass' => 'image-iklan-22', 'idShow' => 'show_iklan_22'])

@include('legacy.ad', ['ad' => $frontService->getArticleById(23), 'imgClass' => 'image-iklan-23', 'idShow' => 'show_iklan_23'])

@if (($frontSys['show_youtube'] ?? 'no') === 'yes')
<style>
.video-container { position: relative; padding-bottom: 56.25%; padding-top: 30px; height: 0; overflow: hidden; }

.video-container iframe, .video-container object, .video-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
</style>
<div class="card mb-3">
                <div class="card-header">
                    Youtube Beranda Hukum
                </div>
                <div class="card-body">
					<div class="video-container">
                    <iframe width="100%" height="auto" src="https://www.youtube.com/embed/hyQVj1RbC-s" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
                    </div>
                </div>
            </div>
@endif
@endsection
