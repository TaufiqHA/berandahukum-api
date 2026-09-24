@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ site_admin('settingscategory') }}">
        @csrf
        <input type="hidden" id="save_url" value="{{ site_admin('settingscategory') }}">
        <ul id="sortable" class="list-group">
            @foreach ($category as $c)
                <li class="list-group-item" id="{{ $c->category_id }}" style="cursor:hand;">{{ $c->category_name }}</li>
            @endforeach
        </ul>
        <button type="button" id="save_btn" class="btn btn-primary mt-3">Simpan Urutan</button>
    </form>
</div>
@endsection
