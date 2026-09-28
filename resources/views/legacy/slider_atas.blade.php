@if (! empty($art_pilihan_top))
<div class="row">
	
	<div class="col-md-12 owl-carousel owl-theme" id="artpilihantop" >
	@foreach ($art_pilihan_top as $a)
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
                        <a href="{{ $url_first }}"><h2 style="color: #000;font-size: 14px;font-weight: 600;">
							{{ $a['article_title'] }}</h2></a>
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

                                var ocImages2 = $("#artpilihantop");

                                ocImages2.owlCarousel({
                                    responsive:{
                                        0:{ items:1 },
                                        480:{ items:2 },
                                        768:{ items:4 },
                                        992:{ items:5 },
                                        1200:{ items:5 }
                                    },
                                    margin: 20,
                                    nav: true,
                                    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                                    rewindNav: true,
                                    dots: false
                                });

                            });

                        </script>
<!--END ARTIKEL PILIHAN ATAS -->
