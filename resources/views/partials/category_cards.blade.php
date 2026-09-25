@if (!empty($frontCategoriesTree))
    <div class="catcards">
        @foreach ($frontCategoriesTree as $cat)
            <section class="catcard">
                <h2 class="catcard__head">
                    <a href="{{ site_url('k/'.$cat['uri']) }}">{{ $cat['name'] }}</a>
                </h2>
                @if (!empty($cat['subs']))
                    <div class="catcard__body">
                        @foreach ($cat['subs'] as $sub)
                            <a class="catcard__row" href="{{ site_url('s/'.$sub['uri']) }}">{{ $sub['name'] }}</a>
                        @endforeach
                    </div>
                @endif
            </section>
        @endforeach
    </div>
@endif
