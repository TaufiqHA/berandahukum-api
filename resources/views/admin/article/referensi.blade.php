@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }} — {{ $article->article_title }}</h4>

    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Sumber</th><th>Judul</th><th>Link</th><th>Urutan</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($datareferensi as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r['sumber'] }}</td>
                <td>{{ $r['sumber'] === 'int' ? $r['article_title'] : $r['judul'] }}</td>
                <td>{{ $r['sumber'] === 'int' ? $r['article_uri'] : $r['link'] }}</td>
                <td>{{ $r['urutan'] }}</td>
                <td><a href="{{ site_admin('article/referensi-delete/'.$r['id']) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="card">
        <div class="card-header">Tambah Referensi</div>
        <div class="card-body">
            <form method="post" action="{{ site_admin('article/referensi-add/'.$article->article_id) }}">
                @csrf
                <div class="form-group">
                    <label>Sumber</label>
                    <select name="sumber" class="form-control">
                        <option value="int">Artikel Internal</option>
                        <option value="ext">Link External</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>ID Artikel Internal (bila sumber = int)</label>
                    <input type="number" name="id_menu" class="form-control">
                </div>
                <div class="form-group">
                    <label>Judul (bila sumber = ext)</label>
                    <input type="text" name="judul" class="form-control">
                </div>
                <div class="form-group">
                    <label>Link (bila sumber = ext)</label>
                    <input type="text" name="link" class="form-control">
                </div>
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="0" required>
                </div>
                <button class="btn btn-primary" name="btn">Simpan</button>
            </form>
        </div>
    </div>
    <a href="{{ site_admin('article') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
