<footer>
    <div class="footer-menu">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="bg-img">
                        <img src="{{ base_url('berandahukum.svg') }}" class="img-fluid" alt="">
                    </div>
                    <p>
                        <div class="satu" >copyright &copy; 2014 - {{ date('Y') }} berandahukum.com</div>
                        
                    </p>
				
                </div>

                {{-- About us, contact us, privacy policy, disclaimer, our partner, Ads info --}}
                <div class="col-md-7">
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <h5 class="footer-title">Informasi</h5>
                            <ul>
                                @foreach ($frontFooterInfo as $r)
                                    <li><a href="{{ site_url('informasi/'.md5($r['setting_id'])) }}">{{ $r['setting_name'] }}</a></li>
                                @endforeach
                                
                            </ul>
                        </div>

                        <div class="col-4">
                            <h5 class="footer-title">Follow Us</h5>
                            <div class="medsos">
                                @foreach ($frontFooterSosial as $f)
                                    <a href="{{ $f['setting_content'] }}" target="blank"><span class="fa fa-{{ $f['setting_name'] }} fa-2x"></span></a>
                                @endforeach
                                <br />
                                <a href="{{ site_url('rss') }}" target="blank"><span class="fa fa-rss fa-2x"></span> RSS </a>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
				<div class="col-md-1 col-sm-12" style="text-align: center;">
					
				<!-- Histats.com  (div with counter) --><div id="histats_counter"></div>
<!-- Histats.com  START  (aync)-->
<script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,4623644,4,436,112,75,00011111']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('//s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<!--<noscript><a href="/" target="_blank"><img  src="//sstatic1.histats.com/0.gif?4623644&101" alt="" border="0"></a></noscript>-->
<!-- Histats.com  END  -->	
				</div>	
            </div>
        </div>
    </div>

</footer>
