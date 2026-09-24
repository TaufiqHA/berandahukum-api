@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('pilihan/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <div class="table-responsive">
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Posisi</th><th>Artikel</th><th>Urutan</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($datapilihan as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r['posisi'] }}</td>
                <td>{{ $r['article_title'] }}</td>
                <td>{{ $r['urutan'] }}</td>
                <td>
                    <a href="{{ site_admin('pilihan/edit/'.$r['id']) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                    <a href="{{ site_admin('pilihan/delete/'.$r['id']) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
