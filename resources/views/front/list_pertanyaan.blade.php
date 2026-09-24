@extends('layouts.front')

@section('content')
<div class="wrap">
    <div class="layout">
        <div class="main">
            <h1>Daftar Pertanyaan</h1>
            <hr class="rule">
            @forelse ($daftarpertanyaan as $p)
                <div class="comment">
                    <p style="font-weight:600;margin-bottom:4px;">{{ $p->pertanyaan }}</p>
                    <p class="comment__time">{{ $p->pertanyaan_nama }} &middot; {{ format_tanggal($p->pertanyaan_date, 'in') }}</p>
                    @if (!empty($p->pertanyaan_jawaban))
                        <div class="comment__reply"><strong>Jawaban:</strong><br>{!! nl2br(e($p->pertanyaan_jawaban)) !!}</div>
                    @endif
                </div>
            @empty
                <div class="empty"><h3>Belum ada pertanyaan</h3><p>Jadilah yang pertama mengirim pertanyaan.</p></div>
            @endforelse

            @include('partials.pager', ['paginator' => $daftarpertanyaan])
        </div>
        @include('partials.side')
    </div>
</div>
@endsection
