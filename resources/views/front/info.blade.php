@extends('layouts.front')

@section('content')
<div class="wrap">
    <div class="layout">
        <div class="main">
            <div class="prose">
                <h1>{{ $setting['setting_name'] }}</h1>
                <hr class="rule">
                {!! $setting['setting_content'] !!}
            </div>
        </div>
        @include('partials.side')
    </div>
</div>
@endsection
