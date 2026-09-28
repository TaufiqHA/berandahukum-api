@php
    $pop = $frontPopup ?? [];
    $popStatus = $pop['ads_status'] ?? 'off';
    $popType = $pop['ads_type'] ?? '2';
    $link_source = $pop['ads_url'] ?? '';
@endphp
@if ($popStatus === 'on')

    @if ($popType === '0')
        {{-- pop_upload : gambar --}}
        @php
            if (is_file(public_path('uploads/i/'.$link_source))) {
                $link_source = base_url('uploads/i/'.$link_source);
            } else {
                $link_source = '';
            }
        @endphp
        @if (($pop['ads_file_type'] ?? '0') == '0' && $link_source !== '')
            <style>
            .white-popup {
                  position: relative;
                  background: #FFF;
                  padding: 20px;
                  width: auto;
                  max-width: 500px;
                  margin: 10% auto;
                }
            </style>
            <div id="foto-upload-ads" class="white-popup">
                <a @if (($pop['ads_link'] ?? '') != '' && $pop['ads_link'] != '#') target="_blank" @endif href="{{ ($pop['ads_link'] ?? '') == '' || $pop['ads_link'] == '#' ? '#' : $pop['ads_link'] }}">
                    <img style="margin-bottom: 10px;width: 100%;" src="{{ $link_source }}" class="img-fluid image-iklan-empat" alt="Responsive image">
                </a>
            </div>
            <script type="text/javascript">
                var cook = getCookie('adspop');
                $(function () {
                    if (window.innerWidth <= 720) return; // popup legacy hanya untuk desktop
                    if(cook != 'ditampilkan'){
                        $.magnificPopup.open({
                          items: {
                            src: '#foto-upload-ads'
                          },
                          type: 'inline'
                        });
                        setCookie('adspop', 'ditampilkan');
                    }
                });
            </script>
        @endif

    @elseif ($popType === '1')
        {{-- pop_embed : video / script --}}
        @if (strpos($link_source, 'iframe') === 0)
            <a class="popup-youtube" href="{{ $link_source }}?autoplay=1" style="display:none;">Open YouTube video</a>
            <script type="text/javascript">
                var cook = getCookie('adspop');
                $(function () {
                    if (window.innerWidth <= 720) return; // popup legacy hanya untuk desktop
                    if(cook != 'ditampilkan'){
                        $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
                            disableOn: 700,
                            type: 'iframe',
                            mainClass: 'mfp-fade',
                            removalDelay: 160,
                            preloader: false,
                            fixedContentPos: false
                        });
                        $('.popup-youtube').click();
                        setCookie('adspop', 'ditampilkan');
                    }
                });
            </script>
        @else
            <style>
            .white-popup {
                  position: relative;
                  background: #FFF;
                  padding: 20px;
                  width: auto;
                  max-width: 500px;
                  margin: 15% auto;
                }
            </style>
            <script type="text/javascript">
                var cook = getCookie('adspop');
                $(function () {
                    if (window.innerWidth <= 720) return; // popup legacy hanya untuk desktop
                    if(cook != 'ditampilkan'){
                        $.magnificPopup.open({
                          items: {
                            src: '<div class="white-popup">{!! $link_source !!}</div>',
                            type: 'inline'
                          }
                        });
                        setCookie('adspop', 'ditampilkan');
                    }
                });
            </script>
        @endif

    @else
        {{-- pop_html : script / html --}}
        <style>
        .white-popup {
              position: relative;
              background: #FFF;
              padding: 25px 20px 20px 20px;
              width: auto;
              max-width: 500px;
              margin: 15% auto;
            }
        </style>
        <div id="ads_script_pop" class="white-popup" style="max-width:550px;margin-bottom: 3px;">{!! $link_source !!}</div>
        <script type="text/javascript">
            var cook = getCookie('adspop');
            $(function () {
                if (window.innerWidth <= 720) return; // popup legacy hanya untuk desktop
                setTimeout(function(){
                    if(cook != 'ditampilkan'){
                        $.magnificPopup.open({
                          items: {
                            src: '#ads_script_pop',
                            type: 'inline'
                          },
                          callbacks: {
                                close: function testt(){
                                    $('#ads_script_pop').addClass('d-none');
                                }
                            }
                        });
                        setCookie('adspop', 'ditampilkan');
                    }else{
                        $('#ads_script_pop').addClass('d-none');
                    }
                }, 1000);
            });
        </script>
    @endif

@endif
