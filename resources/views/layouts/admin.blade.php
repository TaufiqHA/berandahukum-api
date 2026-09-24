<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <title>@yield('title', 'Admin Panel') — Beranda Hukum</title>
    <link rel="stylesheet" href="{{ base_url('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ base_url('assets/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ base_url('assets/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ base_url('assets/summernote/summernote.min.css') }}">
    <link rel="stylesheet" href="{{ base_url('assets/datatables/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ base_url('assets/datatables/responsive/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ url("css/site.css?v=11") }}">
    <link rel="stylesheet" href="{{ url("css/admin.css?v=8") }}">
    <style>
        textarea#articleEditor { height: 350px; }
    </style>
    <script src="{{ base_url('assets/jquery/jquery.min.js') }}"></script>
    <script src="{{ base_url('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ base_url('assets/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ base_url('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ base_url('assets/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ base_url('assets/summernote/summernote.min.js') }}"></script>
    <script src="{{ base_url('assets/select2/js/select2.min.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src="{{ base_url('assets/select2/js/i18n/id.js') }}" type="text/javascript" charset="utf-8"></script>
    <script src="{{ base_url('assets/datatables/responsive/dataTables.responsive.min.js') }}"></script>
    <script src="{{ base_url('assets/datatables/responsive/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/zr2bc6bsbymbpngy195qgi50r7807cxiydmnum10cvutsigz/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    @if (isset($show_ui))
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
    <script src="{{ base_url('assets/jquery.ui.touch-punch.min.js') }}"></script>
    @endif
    <script>
        function saveUrutan(){
            var selectedData = new Array();
            $('#sortable>li').each(function(){ selectedData.push($(this).attr("id")); });
            $.ajax({url:$('#save_url').val(),type:'post',data:{position:selectedData, _token:'{{ csrf_token() }}'},success:function(){location.reload();}});
        }
        $(function(){ if($('#sortable').length > 0){ $("#sortable").sortable({placeholder:"ui-state-highlight",cursor:"hand"}); $("#save_btn").click(function(){saveUrutan();}); } });
    </script>
</head>
<body class="admin">
    <div class="admin-shell">
        @include('partials.admin_menu')
        <div class="admin-backdrop" id="adminBackdrop" hidden></div>

        <div class="admin-main">
            <header class="admin-topbar">
                <button class="admin-burger" type="button" aria-label="Buka menu" aria-controls="adminSidebar" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <span class="admin-topbar__title">@yield('title', 'Admin Panel')</span>
                <div class="admin-topbar__right">
                    <span class="admin-user" title="{{ auth()->user()->user_name ?? '' }}">
                        <span class="admin-user__avatar"><i class="fa fa-user"></i></span>
                        <span class="admin-user__name">{{ auth()->user()->user_name ?? '' }}</span>
                    </span>
                    <a class="admin-logout" href="{{ site_admin('logout') }}">
                        <i class="fa fa-sign-out"></i><span>Keluar</span>
                    </a>
                </div>
            </header>

            <main class="admin-content">
                @if (session('msg_flash'))
                    {!! session('msg_flash') !!}
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            var sidebar = document.getElementById('adminSidebar');
            var burger = document.querySelector('.admin-burger');
            var backdrop = document.getElementById('adminBackdrop');

            function setOpen(open) {
                if (!sidebar) return;
                sidebar.classList.toggle('is-open', open);
                if (backdrop) {
                    backdrop.hidden = !open;
                    backdrop.classList.toggle('is-active', open);
                }
                if (burger) burger.setAttribute('aria-expanded', open ? 'true' : 'false');
            }

            function isMobile() {
                return window.matchMedia('(max-width: 900px)').matches;
            }

            burger?.addEventListener('click', function () {
                setOpen(!sidebar.classList.contains('is-open'));
            });

            // Klik di luar sidebar (area gelap / konten) menutup sidebar.
            backdrop?.addEventListener('click', function () { setOpen(false); });

            // Klik di area konten juga menutup sidebar saat mode mobile.
            document.querySelector('.admin-main')?.addEventListener('click', function (e) {
                if (isMobile() && sidebar.classList.contains('is-open')) {
                    if (!e.target.closest('.admin-burger')) setOpen(false);
                }
            });

            // Tekan Esc untuk menutup.
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && sidebar.classList.contains('is-open')) setOpen(false);
            });

            // Tutup otomatis saat layar kembali ke desktop.
            window.addEventListener('resize', function () {
                if (!isMobile()) setOpen(false);
            });
        })();
    </script>
    <script>
        // Tampilkan nama file yang dipilih pada field upload.
        document.querySelectorAll('.file-field__input').forEach(function (input) {
            input.addEventListener('change', function () {
                var label = input.closest('.file-field')?.querySelector('.file-field__name');
                if (!label) return;
                var file = input.files && input.files.length ? input.files[0] : null;
                if (file) {
                    label.textContent = file.name;
                    label.classList.add('has-file');
                } else {
                    label.textContent = label.dataset.empty || 'Belum ada file dipilih';
                    label.classList.remove('has-file');
                }
            });
        });
    </script>
    <script>
    var useDarkMode = false;
    tinymce.init({
      selector: '#articleEditor',
      plugins: 'print preview importcss searchreplace autolink autosave save directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount textpattern noneditable help charmap quickbars emoticons code',
      menubar: 'file edit view insert format tools table help',
      toolbar: 'code | undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save print | insertfile image media link anchor codesample',
      height: 600,
      image_caption: true,
      quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
      content_style: '.neweditor{ color: gray; }',
      skin: useDarkMode ? 'oxide-dark' : 'oxide',
      content_css: useDarkMode ? 'dark' : 'default',
    });
    </script>
</body>
</html>
