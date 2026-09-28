<div class="row">
    <div class="col-md-12">
        <div id="side-menu">
            @foreach ([8, 9, 10, 16, 17, 18, 19, 20] as $pos)
                @php
                    $nama = [8 => 'delapan', 9 => 'sembilan', 10 => 'sepuluh', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20'][$pos];
                    $embedHeight = in_array($pos, [9, 10, 16, 17, 18, 19, 20], true) ? '50px' : '';
                @endphp
                @include('legacy.ad', [
                    'ad' => $frontService->getArticleById($pos),
                    'imgClass' => 'img-fluid',
                    'idShow' => 'show_iklan_'.$nama,
                    'embedHeight' => $embedHeight,
                ])
            @endforeach

            @if (($frontSys['show_pertanyaan'] ?? null) === 'yes')
            <p style="display:block;text-align:center;">
                <a href="{{ site_url('kirimpertanyaan') }}" class="button-pertanyaan" style="margin: 5px auto;">Kirim Pertanyaan</a>
            </p>
            @endif

            <style>
            .card-body a, a:active{ color: #55504F; font-weight:600; }
            </style>
            @foreach ($frontService->categories() as $c)
                @php
                    $category_id = $c['category_id'];
                    $sc_count = $c['sc_count'];
                @endphp

                @if ($sc_count > 0 && ($c['category_show'] ?? 'no') === 'yes')
                    <div class="card mb-3">
                        <div class="card-header">
                            {{ $c['category_name'] }}
                        </div>
                        <div class="card-body">
                            @foreach ($frontService->sideSubCategories((int) $category_id) as $sc)
                                @if (($sc['sub_category_show'] ?? 'no') === 'yes')
                                    @php $sub_category_id = $sc['sub_category_id']; @endphp
                                    <div class="form-group">
                                        <select onchange="changeArticle(this.options[this.selectedIndex])" class="form-control select2">
                                            <option value="" disabled selected>{{ $sc['sub_category_name'] }}</option>
                                            @foreach ($frontService->sideSubCategoryArticles((int) $category_id, (int) $sub_category_id) as $a)
                                                <option value="{{ $a['article_uri'] }}">{{ $a['article_title'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    </div>
                @elseif (($c['category_show'] ?? 'no') === 'yes')
                    <div class="card mb-3">
                        <div class="card-header">
                            {{ $c['category_name'] }}
                        </div>
                        <div class="card-body" style="padding:10px 10px !Important;  list-style: none !Important;">
                            @php $article = $frontService->sideCategoryArticles((int) $category_id); @endphp
                            @if (! empty($article))
                                <ul style="padding-inline-start: 10px;">
                                    @foreach ($article as $a)
                                        <li style="list-style: none !Important;"><a href="{{ $a['article_uri'] }}" target="_blank">{{ $a['article_title'] }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
