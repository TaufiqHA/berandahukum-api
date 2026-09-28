@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('sub-category') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <p class="text-muted">
        Seret item untuk mengubah urutan sub kategori di dalam kategorinya.
        Klik <strong>Urut Artikel</strong> untuk mengatur urutan artikel pada sub kategori tersebut.
    </p>

    @forelse ($categories as $cat)
        @php $subs = $subCategories[$cat->category_id] ?? collect(); @endphp
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $cat->category_name }}</strong>
                @if ($subs->isNotEmpty())
                    <button type="button" class="btn btn-primary btn-sm sub-save-btn"
                        data-category="{{ $cat->category_id }}"
                        data-url="{{ site_admin('sub-category/urutan') }}">Simpan Urutan</button>
                @endif
            </div>
            <div class="card-body">
                @if ($subs->isEmpty())
                    <span class="text-muted">Belum ada sub kategori.</span>
                @else
                    <ul id="sub-sortable-{{ $cat->category_id }}" class="list-group sub-sortable">
                        @foreach ($subs as $sub)
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                id="{{ $sub->sub_category_id }}" style="cursor:hand;">
                                <span>{{ $sub->sub_category_name }}</span>
                                <a href="{{ site_admin('sub-category/articles/'.$sub->sub_category_id) }}"
                                   class="btn btn-outline-secondary btn-sm">Urut Artikel</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info">Belum ada kategori.</div>
    @endforelse
</div>

<script>
(function ($) {
    $(function () {
        $('.sub-sortable').sortable({ placeholder: 'ui-state-highlight', cursor: 'hand' });

        $('.sub-save-btn').on('click', function () {
            var $btn = $(this);
            var category = $btn.data('category');
            var ids = [];
            $('#sub-sortable-' + category + ' > li').each(function () {
                ids.push($(this).attr('id'));
            });

            $.ajax({
                url: $btn.data('url'),
                type: 'post',
                data: { category_id: category, position: ids, _token: '{{ csrf_token() }}' }
            }).done(function () {
                location.reload();
            }).fail(function () {
                alert('Gagal menyimpan urutan.');
            });
        });
    });
})(jQuery);
</script>
@endsection
