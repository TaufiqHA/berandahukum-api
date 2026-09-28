<div class="row">
    @for ($i = 24; $i <= 35; $i++)
    <div class="col-md-3">
        @include('legacy.ad', [
            'ad' => $frontService->getArticleById($i),
            'imgClass' => 'img-fluid image-iklan-tujuh',
            'idShow' => 'show_iklan_tujuh',
            'width' => '100%',
        ])
    </div>
    @endfor
    
</div>
