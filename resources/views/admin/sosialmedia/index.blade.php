@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>{{ $title }}</h4>
        <a href="{{ site_admin('sosialmedia/add') }}" class="btn btn-primary btn-sm">Tambah</a>
    </div>
    <div class="form-group">
        <label><input type="checkbox" id="toggle-youtube" value="yes" @checked(($setting->show_youtube ?? 'no') === 'yes')> Tampilkan Youtube di beranda</label>
    </div>
    <table class="table table-sm table-bordered table-striped">
        <thead><tr><th>#</th><th>Nama</th><th>Link</th><th>Urutan</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach ($settings as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->setting_name }}</td>
                <td>{{ $r->setting_content }}</td>
                <td>{{ $r->urutan }}</td>
                <td>
                    <a href="{{ site_admin('sosialmedia/edit/'.$r->setting_id) }}" class="btn btn-outline-success btn-sm">Ubah</a>
                    <a href="{{ site_admin('sosialmedia/delete/'.$r->setting_id) }}" onclick="return confirm('Yakin hapus?')" class="btn btn-outline-danger btn-sm">Hapus</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<script>
    document.getElementById('toggle-youtube')?.addEventListener('change', function() {
        fetch("{{ site_admin('sosialmedia/updatestatus') }}", {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({status: this.checked ? 'yes' : 'no'})
        }).then(r => r.json()).then(d => alert(d.message));
    });
</script>
@endsection
