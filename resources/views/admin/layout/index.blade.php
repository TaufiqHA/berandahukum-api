@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="mb-0">{{ $title }}</h4>
    </div>
    <p class="text-muted">Seret untuk mengurutkan, centang untuk menampilkan.</p>

    <form method="post" action="{{ site_admin('layout') }}" id="layoutForm">
        @csrf
        <input type="hidden" id="save_url" value="{{ site_admin('layout') }}">
        <ul id="sortable" class="list-unstyled layout-sort">
            @foreach ($rows as $r)
                <li id="{{ $r['key'] }}" class="layout-sort__item">
                    <span class="layout-sort__handle"><i class="fa fa-bars"></i></span>
                    <span class="layout-sort__label">
                        {{ $r['label'] }}
                        <span class="badge badge-light border text-uppercase" style="font-size:10px;">{{ $r['scope'] }}</span>
                    </span>
                    <label class="layout-sort__toggle mb-0">
                        <input type="checkbox" name="enabled[]" value="{{ $r['key'] }}" @checked($r['enabled'])>
                        Tampilkan
                    </label>
                </li>
            @endforeach
        </ul>
        <div id="hiddenOrder"></div>
        <button type="submit" name="btn" class="btn btn-primary mt-3">Simpan Tata Letak</button>
    </form>
</div>

<style>
    .layout-sort__item { display: flex; align-items: center; gap: 12px; padding: 12px 14px; margin-bottom: 8px; background: #fff; border: 1px solid #dee2e6; border-left: 4px solid var(--brand, #c1121f); }
    .layout-sort__handle { cursor: grab; color: #9b9a93; }
    .layout-sort__label { flex: 1 1 auto; min-width: 0; }
    .layout-sort__toggle { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #4c4b46; flex: 0 0 auto; }
    #sortable .ui-state-highlight { height: 44px; margin-bottom: 8px; background: #fbeaea; border: 1px dashed var(--brand, #c1121f); }
</style>

<script>
    document.getElementById('layoutForm').addEventListener('submit', function () {
        var box = document.getElementById('hiddenOrder');
        box.innerHTML = '';
        document.querySelectorAll('#sortable > li').forEach(function (li) {
            var i = document.createElement('input');
            i.type = 'hidden';
            i.name = 'position[]';
            i.value = li.id;
            box.appendChild(i);
        });
    });
</script>
@endsection
