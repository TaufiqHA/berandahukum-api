<nav class="navbar shadow-sm navbar-expand-lg fixed-top navbar-light bg-light clearfix" style="flex-direction: column;">
    <div class="container" >
        <a class="navbar-brand" href="{{ site_url() }}">
            <img src="{{ base_url('berandahukum.svg') }}" class="img-fluid" alt="">
        </a>

        @php
            $iklan_satu = $frontService->getArticleById(1);
            $addHttp = false;
        @endphp
        @if (! empty($iklan_satu))
            @include('legacy.ad', [
                'ad' => $iklan_satu,
                'imgClass' => 'image-top-of-iklan-dua',
                'idShow' => 'show_iklan_satu',
                'aClass' => 'ads-top-menu',
            ])
        @endif


    </div>
    <style>
	.navbar-nav a.nav-link{ 
		font-size: 12px !Important;
		color : #007bff !Important;
		text-decoration: none;
		font-weight: 600;
		margin: 0px 2px 2px 0px !Important;
		padding-left:5px;
		border-radius: 0px !Important; moz-border-radius: 0px !Important;
	}
	.navbar-nav a.active , a.active:hover{ 
		font-size: 12px !Important;
		background-color:#007bff;
		color : #ffffff !Important;
		text-decoration: none;
		padding-left:2px;
		border-radius: 0px !Important; moz-border-radius: 0px !Important;
	}
	.navbar-nav a.nav-link:hover{ 
		background-color:#007bff !Important;
		color : #FFFFFF !Important;
		font-weight: 600;
		border-radius: 0px !Important; moz-border-radius: 0px !Important;
	}
	.nav-link:hover{ border-radius: 0px !Important; moz-border-radius: 0px !Important;}
	
	#btn-panel{margin-left: 5px;}
	.search-show {
	  width: 70%;
	}
	.bg-black{ background-color: #4D4D4D !Important; }
	.fr{ float: right; }
	#input-area { width: 80% !Important; }
	#spacetop{ min-height: 15px; display: block; width: 100% !Important;}
	@media only screen and  (max-width: 460px) {	
		.navbar-nav a.nav-link { width: 100% !Important; }
		#input-area { width: 80% !Important; }
	}	
	@media only screen and  (max-width: 360px) {	
		.navbar-nav a.nav-link { width: 100% !Important; }	
		#input-area { width: 80% !Important; }	
	}	
	</style>
	<div class="container" id="spacetop">
	</div>	
    <div class="container" >
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav" style="flex-direction:row;flex-wrap:wrap;">
                <a style="font-weight: 600;" class="nav-item nav-link {{ request()->segment(2) === null ? 'active' : '' }}" href="{{ site_url() }}"><span class="fa fa-home"></span> Home</a>
            </div>

            <form class="form-inline ml-auto" autocomplete="off" action="{{ site_url('search') }}">
                <div class="input-group" id="input-area">
                    <input type="text" id="search-input" value="{{ request('q') }}" name="q" class="form-control" placeholder="Pencarian">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-search"><span class="fa fa-search"></span></button>
                    </div>
                </div>
                <button type="button" id="btn-panel" class="btn btn-search"><span class="fa fa-search"></span></button>
            </form>
            
        </div>
	</div>	
    
    
    
</nav>
