@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>HP</th><th>Pesan</th><th>Tanggal</th></tr></thead>
        <tbody>
        @foreach ($contact as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->contact_name }}</td>
                <td>{{ $r->contact_email }}</td>
                <td>{{ $r->contact_hp }}</td>
                <td>{{ $r->contact_desc }}</td>
                <td>{{ $r->contact_date }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
