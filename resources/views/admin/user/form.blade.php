@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ $row ? site_admin('user/edit/'.$row->user_id) : site_admin('user/add') }}">
        @csrf
        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="userName" class="form-control" value="{{ old('userName', $row->user_name ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="userEmail" class="form-control" value="{{ old('userEmail', $row->user_email ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>Katasandi {{ $row ? '(kosongkan bila tidak diubah)' : '' }}</label>
            <input type="text" name="userPassword" class="form-control" value="">
        </div>
        <div class="form-group">
            <label>Level</label>
            <select name="userLevel" class="form-control" required>
                <option value="admin" @selected(($row->user_level ?? '') === 'admin')>Admin</option>
                <option value="penulis" @selected(($row->user_level ?? 'penulis') === 'penulis')>Penulis</option>
            </select>
        </div>
        <button class="btn btn-primary" name="btn">Simpan</button>
        <a href="{{ site_admin('user') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
