@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <form method="post" action="{{ site_admin('settingemail/edit') }}">
        @csrf
        @foreach (['smtp_host' => 'SMTP Host', 'smtp_port' => 'SMTP Port', 'smtp_username' => 'SMTP Username', 'smtp_password' => 'SMTP Password', 'smtp_secure' => 'SMTP Secure (tls/ssl)'] as $field => $label)
            <div class="form-group">
                <label>{{ $label }}</label>
                <input type="text" name="{{ $field }}" class="form-control" value="{{ old($field, $setting->$field ?? '') }}" required>
            </div>
        @endforeach
        <button class="btn btn-primary" name="btn">Simpan</button>
    </form>
</div>
@endsection
