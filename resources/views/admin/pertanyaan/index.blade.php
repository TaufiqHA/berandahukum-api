@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Pertanyaan</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @php $rows = \App\Models\Pertanyaan::orderBy('pertanyaan_status')->orderByDesc('pertanyaan_date')->get(); @endphp
        @foreach ($rows as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->pertanyaan_nama }}</td>
                <td>{{ $r->pertanyaan_email }}</td>
                <td>{!! nl2br(e($r->pertanyaan)) !!}@if ($r->pertanyaan_jawaban)<br><b>Jawaban:</b><br>{!! nl2br(e($r->pertanyaan_jawaban)) !!}@endif</td>
                <td>{{ tanggal($r->pertanyaan_date, 'd/m/Y H:i') }}</td>
                <td>{{ $r->pertanyaan_status == 1 ? 'Dijawab' : 'Belum' }}</td>
                <td>
                    <a href="{{ site_admin('pertanyaan/jawab/'.$r->pertanyaan_id) }}" class="btn btn-outline-success btn-sm">Jawab</a>
                    <a href="{{ site_admin('pertanyaan/delete/'.$r->pertanyaan_id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
