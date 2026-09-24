@php
    $pop = $pop ?? null;
    $status = $pop['ads_status'] ?? 'off';
@endphp
@if ($status === 'on')
    @php
        $src = (($pop['ads_type'] ?? '0') == '0') ? url('uploads/i/'.($pop['ads_url'] ?? '')) : ($pop['ads_url'] ?? '');
        $href = ($pop['ads_link'] ?? '') !== '' ? $pop['ads_link'] : '#';
    @endphp
    <div class="popup-overlay" id="popup" role="dialog" aria-modal="true" aria-label="Iklan">
        <div class="popup">
            <button class="popup__close" type="button" aria-label="Tutup">&times;</button>
            @if (($pop['ads_type'] ?? '0') == '0')
                <a href="{{ $href }}" target="_blank" rel="noopener"><img src="{{ $src }}" alt="Iklan"></a>
            @elseif (($pop['ads_type'] ?? '0') == '1')
                {!! $src !!}
            @else
                {!! $pop['ads_content'] ?? '' !!}
            @endif
        </div>
    </div>
    <script>
        (function () {
            var overlay = document.getElementById('popup');
            if (!overlay || document.cookie.indexOf('adspop=') !== -1) return;
            var closeBtn = overlay.querySelector('.popup__close');
            var lastFocus = null;
            function open() {
                lastFocus = document.activeElement;
                overlay.classList.add('is-open');
                closeBtn.focus();
            }
            function close() {
                overlay.classList.remove('is-open');
                document.cookie = 'adspop=1; max-age=' + (10 * 60) + '; path=/';
                if (lastFocus) lastFocus.focus();
            }
            closeBtn.addEventListener('click', close);
            overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && overlay.classList.contains('is-open')) close(); });
            setTimeout(open, 800);
        })();
    </script>
@endif
