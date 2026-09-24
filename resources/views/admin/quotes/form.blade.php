@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('quotes/edit/'.md5($row->quote_id)) : site_admin('quotes/add') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Gambar Quote</label>
            @include('partials.file_field', ['name' => 'quote_image', 'accept' => 'image/*', 'current' => $row->quote_image ?? null])
        </div>
        <div class="form-group">
            <label>Urutan</label>
            <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $row->urutan ?? 0) }}" required>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="quoteStatus" value="yes" @checked(($row->quote_status ?? '1') == '1')> Tampilkan</label>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('quotes') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
