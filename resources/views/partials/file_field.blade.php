@php
    $accept = $accept ?? null;
    $current = $current ?? null;
    $emptyText = $emptyText ?? 'Belum ada file dipilih';
@endphp
<label class="file-field">
    <input type="file" name="{{ $name }}" class="file-field__input" @if ($accept) accept="{{ $accept }}" @endif>
    <span class="file-field__btn"><i class="fa fa-upload"></i> Pilih File</span>
    <span class="file-field__name" data-empty="{{ $emptyText }}">{{ $emptyText }}</span>
</label>
@if (!empty($current))
    <small class="file-field__current">File saat ini: {{ $current }}</small>
@endif
