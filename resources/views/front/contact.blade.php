@extends('layouts.front')

@section('content')
<div class="wrap">
    <div class="layout">
        <div class="main">
            <h1>Hubungi Kami</h1>
            <p class="muted">Sampaikan kritik dan saran Anda untuk Beranda Hukum.</p>
            <hr class="rule">

            @if (session('msg_flash'))
                {!! session('msg_flash') !!}
            @endif

            <form action="{{ site_url('contact') }}" method="post" style="max-width:560px;">
                @csrf
                <div class="field">
                    <label for="contactName">Nama Lengkap</label>
                    <input type="text" id="contactName" name="contactName" value="{{ old('contactName') }}" required>
                </div>
                <div class="field">
                    <label for="contactEmail">Email</label>
                    <input type="email" id="contactEmail" name="contactEmail" value="{{ old('contactEmail') }}" required>
                </div>
                <div class="field">
                    <label for="contactHP">Nomor HP</label>
                    <input type="text" id="contactHP" name="contactHP" value="{{ old('contactHP') }}" required>
                </div>
                <div class="field">
                    <label for="contactDesc">Kritik dan Saran</label>
                    <textarea id="contactDesc" name="contactDesc" rows="5" required>{{ old('contactDesc') }}</textarea>
                </div>
                <button type="submit" class="btn btn--primary">Kirim</button>
            </form>
        </div>
        @include('partials.side')
    </div>
</div>
@endsection
