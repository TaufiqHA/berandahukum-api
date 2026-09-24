@extends('layouts.front')

@section('content')
<div class="wrap">
    <div class="layout">
        <div class="main">
            <h1>Kirim Pertanyaan</h1>
            <p class="muted">Sampaikan pertanyaan hukum Anda. Jawaban akan tampil pada daftar pertanyaan.</p>
            <hr class="rule">

            @if (session('msg_flash'))
                {!! session('msg_flash') !!}
            @endif

            <form action="{{ site_url('kirimpertanyaan') }}" method="post" style="max-width:560px;">
                @csrf
                <div class="field">
                    <label for="tanyaNama">Nama Lengkap</label>
                    <input type="text" id="tanyaNama" name="tanyaNama" value="{{ old('tanyaNama') }}" required>
                </div>
                <div class="field">
                    <label for="tanyaEmail">Email</label>
                    <input type="email" id="tanyaEmail" name="tanyaEmail" value="{{ old('tanyaEmail') }}" required>
                </div>
                <div class="field">
                    <label for="pertanyaan">Pertanyaan</label>
                    <textarea id="pertanyaan" name="pertanyaan" rows="5" required>{{ old('pertanyaan') }}</textarea>
                </div>
                <button type="submit" name="btn_kirim" value="1" class="btn btn--primary">Kirim Pertanyaan</button>
            </form>

            <hr class="rule">
            <div class="section__head"><h2>Daftar Pertanyaan</h2></div>
            @forelse ($daftarpertanyaan as $p)
                <div class="comment">
                    <p style="font-weight:600;margin-bottom:4px;">{{ $p->pertanyaan }}</p>
                    <p class="comment__time">{{ $p->pertanyaan_nama }} &middot; {{ format_tanggal($p->pertanyaan_date, 'in') }}</p>
                    @if (!empty($p->pertanyaan_jawaban))
                        <div class="comment__reply"><strong>Jawaban:</strong><br>{!! nl2br(e($p->pertanyaan_jawaban)) !!}</div>
                    @endif
                </div>
            @empty
                <div class="empty">Belum ada pertanyaan.</div>
            @endforelse
        </div>
        @include('partials.side')
    </div>
</div>
@endsection
