<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="xvqI3mYc-iOX3n-dkgrBpm96bP3cmV4u5imTavVbSEQ" />
    <title>{{ $title ?? 'Beranda Hukum' }}</title>
    <meta property="og:locale" content="id_ID" />
	<meta property="og:type" content="{{ $og_type ?? 'website' }}" />
	<meta property="og:title" content="{{ $og_title ?? ($title ?? 'Beranda Hukum') }}" />
	<meta property="og:description" content="{{ $og_description ?? 'berandahukum.com adalah sebuah wadah dalam bentuk website yang dikelola sebagai sarana untuk belajar hukum, menambah wawasan hukum dan sarana berbagi tentang hukum.' }}" />
	<meta property="og:image" content="{{ base_url('assets/img/logo-share.jpeg') }}" />
	<meta property="og:image:secure_url" content="{{ base_url('assets/img/logo-share.jpeg') }}" />
	<meta property="og:image:alt" content="berandahukum.com" />
	<meta property="og:url" content="{{ $og_url ?? url('/') }}" />
	<meta property="og:site_name" content="Berandahukum.com" />
	<meta property="article:section" content="BerandaHukum" />
	
	<meta name="twitter:card" content="{{ $twitter_type ?? 'summary' }}" />
	<meta name="twitter:title" content="{{ $og_title ?? ($title ?? 'Beranda Hukum') }}" />
	<meta name="twitter:description" content="{{ $og_description ?? 'berandahukum.com adalah sebuah wadah dalam bentuk website yang dikelola sebagai sarana untuk belajar hukum, menambah wawasan hukum dan sarana berbagi tentang hukum.' }}" />
	<meta name="twitter:site" content="@berandahukum" />
    <link rel="icon" type="image/png" href="{{ base_url('favicon.png?v=0.0.1') }}" />
    {{-- CSS desain referensi (Bootstrap) — hanya untuk desktop --}}
    <link rel="stylesheet" href="{{ base_url('assets/bootstrap/css/bootstrap.min.css') }}" media="(min-width: 721px)">
    <link rel="stylesheet" href="{{ base_url('assets/select2/css/select2.min.css') }}" media="(min-width: 721px)">
    <link rel="stylesheet" href="{{ base_url('assets/select2/css/select2-bootstrap4.min.css') }}" media="(min-width: 721px)">
    <link rel="stylesheet" href="{{ base_url('assets/owl-carousel2/owl.carousel.min.css') }}" media="(min-width: 721px)">
    <link rel="stylesheet" href="{{ base_url('assets/owl-carousel2/owl.theme.default.min.css') }}" media="(min-width: 721px)">
    <link rel="stylesheet" href="{{ base_url('assets/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ base_url('assets/jssocials/jssocials.css') }}" media="(min-width: 721px)">
    <link rel="stylesheet" href="{{ base_url('assets/jssocials/jssocials-theme-flat.css') }}" media="(min-width: 721px)">
    <link rel="stylesheet" href="{{ base_url('assets/css/stylenew.css?v=98.1.8') }}" media="(min-width: 721px)">
    <link rel="stylesheet" href="{{ base_url('assets/responsiveslides.css') }}" media="(min-width: 721px)">
    @if (($frontPopStatus ?? 'off') === 'on')
    <link rel="stylesheet" href="{{ base_url('assets/magnific/magnific-popup.css') }}" media="(min-width: 721px)">
    @endif
    {{-- CSS desain lama — hanya untuk mobile --}}
    <link rel="stylesheet" href="{{ url('css/site.css?v=28') }}" media="(max-width: 720px)">
    <style>
		.button-pertanyaan{
			background-color: #FF0000 !important;
			border: none!important;
			border-radius: 1px!important;
			box-shadow: none!important;
			color: #fff!important;
			cursor: pointer;
			font-family: 'Open Sans',Arial,Helvetica,sans-serif!important;
			font-size: 18px!important;
			font-weight: 700!important;
			line-height: 21px!important;
			height: auto;
			padding: 10px!important;
			width: 98% !important;
			display:block;
			box-sizing: border-box!important;
		}
        .jssocials-share-link { border-radius: 50%; }
		.iBottom,
        .iMiddle {
            padding: 30px 0;
        }

        .iTop {
            padding-bottom: 30px;
        }
		
		.img-i {
            max-height: 150px;
        }
        .clearfix::after{
		 content: "";
		 clear: both;
		 display: table;
		}
		.content-hukum {
			margin-top: 30px;
		}
		.top-gap-85{margin-top: 85px !Important;}
		.top-gap-40{margin-top: 65px !Important;}
		.navbar-brand > img {
			width: 300px !Important;
			height: auto;
			padding: 0 10px;
			
		}
		.content-left{ padding-bottom: 30px;}
		.ads-top-menu{ width: 75%; display:block; }
		@media only screen and  (max-width: 360px) {	
			.ads-top-menu{ width: 100% !Important; height: auto; display:block;}
			.ads-top-menu img{ width: 100% !Important;  height: auto; object-fit:cover; display:block;}
			.navbar-brand > img {
				width: 100% !Important;
				height: auto;
				padding: 0 10px;
				
			}
		}	
		
		
		@media only screen and  (max-width: 460px) {	
			.ads-top-menu{ width: 100% !Important; height: auto; display:block;}
			.ads-top-menu img{ width: 100% !Important; height: auto; object-fit:cover; display:block;}
			.navbar-brand > img {
				width: 100% !Important;
				height: auto;
				padding: 0 10px;
				
			}
			
			#artpilihantop { display: none !Important; }
			.button-pertanyaan{ font-size: 16px!important; padding: 8px!important; }
		}
		
    
		.owl-prev, .owl-next {
				width: 20px;
				height: 35px;
				position: absolute;
				top: 30%;
				transform: translateY(-50%);
				display: block !important;
				border:0px solid black;
				background-color: #000 !Important;
				color: #fff !Important;
			}
			.owl-prev { left: 0px; }
			.owl-next { right: 0px; }
			.owl-prev i, .owl-next i {transform : scale(2,2); color: #ccc;}
			.image-ap { width:100%;height: 150px; } 
			@media only screen and  (max-width: 360px) {	
				.image-ap { width:100%;height: 170px; } 
				#artpilihantop { display: none !Important; }
				.button-pertanyaan{ font-size: 16px!important; padding: 8px!important; }
			}	

		/* Hybrid: desktop = desain referensi, mobile (HP/tablet) = desain lama. */
		.mobile-home { display: none; }
		@media (max-width: 720px) {
			.legacy-home { display: none !important; }
			.mobile-home { display: block; }
		}
		
</style>
   
    <script>
        var site_url = '{{ site_url() }}';
    </script>
    <script src="{{ base_url('assets/jquery/jquery.min.js') }}"></script>
    <script src="{{ base_url('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ base_url('assets/jssocials/jssocials.min.js') }}"></script>
    
</head>
<body>
    <div class="legacy-home">
    @include('legacy.menu')
   
    
	<div id="divTop"></div>
    <div class="content-hukum">
        <div class="container">
            <div id="space_top" class="row {{ $frontClassTop ?? 'top-gap-40' }}">
				<div class="col-md-12">
					@include('legacy.slider_atas')
				</div>	
                <div class="col-md-8 content-left">
                    @yield('content')
                </div>
                <div class="col-md-4">
                    @include('legacy.side')
                </div>
				<div class="col-md-12">
					@include('legacy.slider_bawah')
				</div>	
            </div>
        </div>
    </div>
    <div id="divBottom"></div>

    @include('legacy.footer')
    </div>

    {{-- Tampilan mobile: desain lama (Modern Magazine) agar UI mobile tidak berubah. --}}
    <div class="mobile-home">
        @include('mobile.home')
    </div>
	
    <script src="{{ base_url('assets/select2/js/select2.min.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src="{{ base_url('assets/select2/js/i18n/id.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src="{{ base_url('assets/owl-carousel2/owl.carousel.min.js') }}"></script>
    <script src="{{ base_url('assets/js/jssor.slider-28.1.0.min.js') }}"></script>
    <script src="{{ base_url('assets/js/main.js?v=0.0.3') }}"></script>
    <script src="{{ base_url('assets/responsiveslides.min.js') }}"></script>
    @if (($frontPopStatus ?? 'off') === 'on')
	<script src="{{ base_url('assets/magnific/jquery.magnific-popup.min.js') }}"></script>
    @endif
    <script>
        $(".divShare").jsSocials({
            showLabel: false,
            showCount: true,
            shares: ["twitter", "facebook", "linkedin", "whatsapp", "telegram", "line", "messenger"]
        });

      $(function () {
	
		if($('#msg_error').length > 0){
			$('#msg_error').hide();
		}
		
		$('#input-area').hide();
		$('#btn-panel').click(function(){
			var str_btn = $('#btn-panel').html();
			if(str_btn == '<span class="fa fa-search"></span>'){
				$('#input-area').show("fast");
				$('#search-input').addClass('search-show');
				$('#btn-panel').addClass('bg-black');
				$('#btn-panel').addClass('fr');
				$('#btn-panel').html('<span class="fa fa-close"></span>');
				if($('#menu_len').val() > 75 && $('#menu_len').val() < 99){
					$('#space_top').removeClass('top-gap-40');
					$('#space_top').addClass('top-gap-85');
				}
			}else{
				$('#input-area').hide("fast");
				$('#search-input').removeClass('search-show');
				$('#btn-panel').removeClass('bg-black');
				$('#btn-panel').removeClass('fr');
				$('#btn-panel').html('<span class="fa fa-search"></span>');
				if($('#menu_len').val() > 75 && $('#menu_len').val() < 99){
					$('#space_top').removeClass('top-gap-85');
					$('#space_top').addClass('top-gap-40');
				}
			}
			
		});

    
      // Slideshow 4
      if (window.innerWidth > 720) {
      $("#slider4").responsiveSlides({
        auto: true,
        pager: false,
        nav: true,
        speed: 500,
        namespace: "callbacks",
        before: function () {
          $('.events').append("<li>before event fired.</li>");
        },
        after: function () {
          $('.events').append("<li>after event fired.</li>");
        }/**/
      });
      }

    }); 
    function deleteCookie(cName, cValue) {
							let date = new Date();
							date.setTime(date.getTime() + (2 * 1000));
							const expires = "expires=" + date.toUTCString();
							document.cookie = cName + "=" + cValue + "; " + expires + "; path=/";
					}
    function setCookie(cName, cValue) {
							let date = new Date();
							date.setTime(date.getTime() + (10 * 60 * 1000));
							const expires = "expires=" + date.toUTCString();
							document.cookie = cName + "=" + cValue + "; " + expires + "; path=/";
					}
	function getCookie(cName) {
		  const name = cName + "=";
		  const cDecoded = decodeURIComponent(document.cookie); //to be careful
		  const cArr = cDecoded .split('; ');
		  let res;
		  cArr.forEach(val => {
			  if (val.indexOf(name) === 0) res = val.substring(name.length);
		  })
		  return res;
	}
		
	if (window.innerWidth > 720) { $(".rslides").responsiveSlides(); }
    </script>
    @include('legacy.popup')
    
</body>
</html>
