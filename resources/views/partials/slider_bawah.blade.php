@php $ada = false; @endphp
@foreach (range(24, 35) as $i)
    @if ($frontService->getArticleById($i)) @php $ada = true; @endphp @endif
@endforeach
@if ($ada)
    <section class="band band--tint">
        <div class="wrap">
            <div class="section__head">
                <span class="section__no">+</span>
                <h2 class="section__title">Mitra</h2>
                <span class="section__rule"></span>
            </div>
            <div class="grid grid--3">
                @foreach (range(24, 35) as $i)
                    @php $ad = $frontService->getArticleById($i); @endphp
                    @if ($ad)
                        <div>@include('partials.ad', ['ad' => $ad])</div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
