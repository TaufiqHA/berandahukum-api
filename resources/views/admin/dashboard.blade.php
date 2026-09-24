@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="admin-page-head"><h1>Dashboard</h1></div>

<div class="row">
    <div class="col-md-4">
        <div class="card"><div class="card-body stat">
            <div class="stat__label">Total Artikel</div>
            <div class="stat__value">{{ $total_artikel }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body stat">
            <div class="stat__label">Artikel Draft</div>
            <div class="stat__value">{{ $total_draft }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body stat">
            <div class="stat__label">Artikel Publish</div>
            <div class="stat__value">{{ $total_publish }}</div>
        </div></div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card"><div class="card-body stat">
            <div class="stat__label">Komentar Masuk</div>
            <div class="stat__value">{{ $total_komentar }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body stat">
            <div class="stat__label">Komentar Terverifikasi</div>
            <div class="stat__value">{{ $total_komentar_ver }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body stat">
            <div class="stat__label">Belum Verifikasi</div>
            <div class="stat__value">{{ $total_komentar_nonver }}</div>
        </div></div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Artikel Berdasarkan Kategori</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>No</th><th>Kategori</th><th>Jumlah</th></tr></thead>
                        <tbody>
                        @forelse ($artikelkategori as $k)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $k->category_name }}</td><td>{{ $k->jumlah }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="muted">Belum ada data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Artikel Berdasarkan Penulis</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>No</th><th>Penulis</th><th>Jumlah</th></tr></thead>
                        <tbody>
                        @forelse ($artikelpenulis as $k)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $k->penulis }}</td><td>{{ $k->jumlah }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="muted">Belum ada data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
