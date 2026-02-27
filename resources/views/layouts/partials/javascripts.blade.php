<script type="text/javascript">
    base_path = "{{ url('/') }}";
    //used for push notification
    APP = {};
    APP.PUSHER_APP_KEY = '{{ config('broadcasting.connections.pusher.key') }}';
    APP.PUSHER_APP_CLUSTER = '{{ config('broadcasting.connections.pusher.options.cluster') }}';
    APP.INVOICE_SCHEME_SEPARATOR = '{{ config('constants.invoice_scheme_separator') }}';
    //variable from app service provider
    APP.PUSHER_ENABLED = '{{ $__is_pusher_enabled }}';
    @auth
    @php
        $user = Auth::user();
    @endphp
    APP.USER_ID = "{{ $user->id }}";
    @else
        APP.USER_ID = '';
    @endauth
</script>

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js?v=$asset_v"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js?v=$asset_v"></script>
<![endif]-->

<script src="{{ asset('js/vendor.js?v=' . $asset_v) }}"></script>

@if (file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@else
    <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
@endif
@php
    $business_date_format = session('business.date_format', config('constants.default_date_format'));
    $datepicker_date_format = str_replace('d', 'dd', $business_date_format);
    $datepicker_date_format = str_replace('m', 'mm', $datepicker_date_format);
    $datepicker_date_format = str_replace('Y', 'yyyy', $datepicker_date_format);

    $moment_date_format = str_replace('d', 'DD', $business_date_format);
    $moment_date_format = str_replace('m', 'MM', $moment_date_format);
    $moment_date_format = str_replace('Y', 'YYYY', $moment_date_format);

    $business_time_format = session('business.time_format');
    $moment_time_format = 'HH:mm';
    if ($business_time_format == 12) {
        $moment_time_format = 'hh:mm A';
    }

    $common_settings = !empty(session('business.common_settings')) ? session('business.common_settings') : [];

    $default_datatable_page_entries = !empty($common_settings['default_datatable_page_entries'])
        ? $common_settings['default_datatable_page_entries']
        : 25;
@endphp

<script>
    Dropzone.autoDiscover = false;
    moment.tz.setDefault('{{ Session::get('business.time_zone') }}');
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        @if (config('app.debug') == false)
            $.fn.dataTable.ext.errMode = 'throw';
        @endif
    });

    var financial_year = {
        start: moment('{{ Session::get('financial_year.start') }}'),
        end: moment('{{ Session::get('financial_year.end') }}'),
    }
    @if (file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
        //Default setting for select2
        $.fn.select2.defaults.set("language", "{{ session()->get('user.language', config('app.locale')) }}");
    @endif

    var datepicker_date_format = "{{ $datepicker_date_format }}";
    var moment_date_format = "{{ $moment_date_format }}";
    var moment_time_format = "{{ $moment_time_format }}";

    var app_locale = "{{ session()->get('user.language', config('app.locale')) }}";

    var non_utf8_languages = [
        @foreach (config('constants.non_utf8_languages') as $const)
            "{{ $const }}",
        @endforeach
    ];

    var __default_datatable_page_entries = "{{ $default_datatable_page_entries }}";

    var __new_notification_count_interval = "{{ config('constants.new_notification_count_interval', 60) }}000";
</script>

@if (file_exists(public_path('js/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script src="{{ asset('js/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@else
    <script src="{{ asset('js/lang/en.js?v=' . $asset_v) }}"></script>
@endif

<script src="{{ asset('js/functions.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/common.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/app.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/help-tour.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/documents_and_note.js?v=' . $asset_v) }}"></script>

<!-- TODO -->
@if (file_exists(public_path('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js')))
    <script
        src="{{ asset('AdminLTE/plugins/select2/lang/' . session()->get('user.language', config('app.locale')) . '.js?v=' . $asset_v) }}">
    </script>
@endif
@php
    $validation_lang_file = 'messages_' . session()->get('user.language', config('app.locale')) . '.js';
@endphp
@if (file_exists(public_path() . '/js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file))
    <script src="{{ asset('js/jquery-validation-1.16.0/src/localization/' . $validation_lang_file . '?v=' . $asset_v) }}">
    </script>
@endif

@if (!empty($__system_settings['additional_js']))
    {!! $__system_settings['additional_js'] !!}
@endif
@yield('javascript')

@if (Module::has('Essentials'))
    @includeIf('essentials::layouts.partials.footer_part')
@endif

<script type="text/javascript">
    $(document).ready(function() {
        var locale = "{{ session()->get('user.language', config('app.locale')) }}";
        var isRTL =
            @if (in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')))
                true;
            @else
                false;
            @endif

        $('#calendar').fullCalendar('option', {
            locale: locale,
            isRTL: isRTL
        });
        // side bar toggle  
        $(".drop_down").click(function(event) {
            event.preventDefault();
            var $chiled = $(this).next(".chiled");
            var svgElement = $(this).find(".svg");
            $(".chiled").not($chiled).slideUp();
            $chiled.slideToggle(function() {
                $(".svg").each(function() {
                    var $currentSvgElement = $(this);
                    if ($currentSvgElement.closest(".drop_down").next(".chiled").is(
                            ":visible")) {
                        // If the corresponding menu is visible, set the arrow pointing upwards
                        $currentSvgElement.html(
                            '<path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M6 9l6 6l6 -6" />'
                        );
                    } else {
                        // Otherwise, set the arrow pointing downwards
                        $currentSvgElement.html(
                            '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" />'
                        );
                    }
                });
            });
        });

        $('.small-view-button').on('click', function() {
            $('.side-bar').addClass('small-view-side-active');
            $('.overlay').fadeIn('slow');
        });

        $('.overlay').on('click', function() {
            $('.overlay').fadeOut('slow');
            $('.side-bar').removeClass('small-view-side-active');
        });

        $(window).on('resize', function() {
            if ($(window).width() >= 992) {
                $('.overlay').fadeOut('slow');
                $('.side-bar').removeClass('small-view-side-active');
            }

            if($('.side-bar').hasClass('small-view-side-active')){
                $('.overlay').fadeIn('slow');
            }
        });

        $(document).on('click', function (e) {
            $('[data-toggle="popover"]').popover();

            $(document).on('click', function (e) {
                $('[data-toggle="popover"]').each(function () {
                    // Check if the clicked element is the popover button or inside the popover
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
            });
            
        });

        $('.side-bar-collapse').click(function() {
            $('.side-bar').toggle('slow');
        });

        $('.dt-buttons.btn-group').find('a.btn').removeClass('btn-default');
        $('.dt-buttons.btn-group').find('a.btn').removeClass('btn');
   
    });

    /* ============================================================
       WORKFLOW SPEED MODULE — Keyboard shortcuts, auto-focus, etc.
    ============================================================ */
    $(document).ready(function() {

        // ── 1. KEYBOARD SHORTCUTS ─────────────────────────────────
        // Show a floating shortcut hint bar (fade-in then fade-out once per session)
        if (!sessionStorage.getItem('kbd_hint_shown')) {
            var $hint = $('<div id="kbd_shortcut_hint"></div>').css({
                position: 'fixed', bottom: '24px', left: '50%', transform: 'translateX(-50%)',
                background: 'rgba(30,30,40,0.92)', color: '#fff', borderRadius: '10px',
                padding: '10px 22px', fontSize: '13px', zIndex: 99999,
                boxShadow: '0 4px 24px rgba(0,0,0,0.25)', letterSpacing: '0.3px',
                opacity: 0, transition: 'opacity 0.4s'
            }).html(
                '⌨️ Raccourcis : <b>Alt+H</b> Accueil &nbsp;|&nbsp; <b>Alt+P</b> POS &nbsp;|&nbsp; <b>Alt+C</b> Clients &nbsp;|&nbsp; <b>Alt+N</b> Nouveau client &nbsp;|&nbsp; <b>Alt+D</b> Ventes &nbsp;|&nbsp; <b>/</b> Rechercher &nbsp;|&nbsp; <b>Ctrl+↵</b> Valider &nbsp;|&nbsp; <b>Esc</b> Fermer'
            ).appendTo('body');
            setTimeout(function(){ $hint.css('opacity', 1); }, 300);
            setTimeout(function(){ $hint.css('opacity', 0); setTimeout(function(){ $hint.remove(); }, 500); }, 6000);
            sessionStorage.setItem('kbd_hint_shown', '1');
        }

        $(document).on('keydown', function(e) {
            var tag = (e.target.tagName || '').toLowerCase();
            var isInput = (tag === 'input' || tag === 'textarea' || tag === 'select');

            // Alt + key shortcuts (work from anywhere)
            if (e.altKey && !e.ctrlKey && !e.shiftKey) {
                switch(e.key.toLowerCase()) {
                    case 'h': // Home / Dashboard
                        e.preventDefault();
                        window.location.href = base_path + '/home';
                        break;
                    case 'p': // POS Sale
                        e.preventDefault();
                        window.location.href = base_path + '/pos/create';
                        break;
                    case 'c': // Customers list
                        e.preventDefault();
                        window.location.href = base_path + '/contacts?type=customer';
                        break;
                    case 'n': // New Customer modal — click the first .btn-modal with contacts route
                        e.preventDefault();
                        var $addBtn = $('a.btn-modal[data-href*="contacts/create"], a.btn-modal[data-href*="contact/create"]').first();
                        if ($addBtn.length) {
                            $addBtn.trigger('click');
                        } else {
                            window.location.href = base_path + '/contacts?type=customer';
                        }
                        break;
                    case 's': // Sells list
                    case 'd': // Sells list alias (D=Dossiers)
                        e.preventDefault();
                        window.location.href = base_path + '/sells';
                        break;
                    case 'r': // Reports
                        e.preventDefault();
                        window.location.href = base_path + '/reports/profit-loss';
                        break;
                    case 'f': // Focus date/filter field on listing pages
                        e.preventDefault();
                        var $dateFilter = $('[id*="filter_date_range"]:visible').first();
                        if ($dateFilter.length) {
                            $dateFilter.trigger('focus').trigger('click');
                        } else {
                            $('.dataTables_filter input:visible').first().trigger('focus');
                        }
                        break;
                }
            }

            // Ctrl+Enter → submit the current form
            if (e.ctrlKey && (e.key === 'Enter' || e.keyCode === 13)) {
                var $form = null;
                // try focused element's form first
                if (isInput && e.target.form) {
                    $form = $(e.target.form);
                } else {
                    // find the active/visible modal form
                    $form = $('.modal.in form:visible, .modal.show form:visible').first();
                    if (!$form.length) {
                        $form = $('form:visible').first();
                    }
                }
                if ($form && $form.length) {
                    e.preventDefault();
                    // trigger submit button if present for validation hooks
                    var $submitBtn = $form.find('[type="submit"]:not([disabled])').first();
                    if ($submitBtn.length) { $submitBtn.trigger('click'); }
                    else { $form.submit(); }
                }
            }

            // Escape → close topmost open Bootstrap modal
            if (e.key === 'Escape' || e.keyCode === 27) {
                var $modal = $('.modal.in, .modal.show').last();
                if ($modal.length && !isInput) {
                    $modal.modal('hide');
                }
            }

            // "/" → focus the DataTable search box (when not in an input)
            if ((e.key === '/' || e.keyCode === 191) && !isInput && !e.ctrlKey && !e.altKey) {
                var $dtSearch = $('.dataTables_filter input:visible').first();
                if ($dtSearch.length) {
                    e.preventDefault();
                    $dtSearch.trigger('focus').trigger('select');
                }
            }
        });

        // ── 2. AUTO-FOCUS FIRST INPUT ON MODAL OPEN ─────────────
        $(document).on('shown.bs.modal', function(e) {
            var $modal = $(e.target);
            // Focus first visible enabled input (skip hidden, file, checkbox, radio)
            var $first = $modal.find('input:visible:not([type="hidden"]):not([type="file"]):not([type="checkbox"]):not([type="radio"]):not([disabled]):not([readonly]), select:visible:not([disabled]):not([readonly]), textarea:visible:not([disabled]):not([readonly])').first();
            if ($first.length) {
                setTimeout(function() { $first.trigger('focus'); }, 150);
            }
        });

        // ── 3. AUTO-SELECT ON FOCUS (number & text inputs) ───────
        // Makes it fast to overtype an existing value without deleting
        $(document).on('focus', 'input[type="number"], input[type="text"].form-control, input[type="tel"].form-control', function() {
            var self = this;
            setTimeout(function() { $(self).select(); }, 50);
        });

        // ── 4. ENTER KEY ADVANCES TO NEXT FIELD (in prescription sections) ──
        $(document).on('keydown', '.custom-section input, .prescription-table input', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                var $inputs = $('.custom-section input:visible, .prescription-table input:visible');
                var idx = $inputs.index(this);
                if (idx >= 0 && idx < $inputs.length - 1) {
                    $inputs.eq(idx + 1).trigger('focus');
                }
            }
        });

        // ── 5. DATATABLE SEARCH: Esc clears, Enter fires immediately ──
        $(document).on('keydown', '.dataTables_filter input', function(e) {
            if (e.key === 'Escape') {
                $(this).val('').trigger('input');
                e.stopPropagation();
            }
            if (e.key === 'Enter' || e.keyCode === 13) {
                var $input = $(this);
                var $table = $input.closest('.dataTables_wrapper').find('table');
                if ($table.length && $.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().search($input.val()).draw();
                }
                e.stopPropagation();
            }
        });

        // ── 6. DOUBLE-CLICK ANY TABLE ROW → OPEN DETAILS ─────────
        $(document).on('dblclick', 'table.ajax_view tbody tr, #contact_table tbody tr, #sell_table tbody tr', function() {
            var $actionBtn = $(this).find(
                'a[title*="View"], a[title*="Voir"], a[data-href*="/show"], ' +
                'a[href*="/show"], a[title*="Edit"], a[title*="Modifier"]'
            ).first();
            if (!$actionBtn.length) {
                $actionBtn = $(this).find('td:first-child a').first();
            }
            if ($actionBtn.length) {
                if ($actionBtn.hasClass('btn-modal') || $actionBtn.data('href')) {
                    $actionBtn.trigger('click');
                } else if ($actionBtn.attr('href') && $actionBtn.attr('href') !== '#') {
                    window.location.href = $actionBtn.attr('href');
                } else {
                    $actionBtn.trigger('click');
                }
            }
        });

        // ── 7. SUBMIT BUTTON SPINNER (prevent double-submit) ─────
        $(document).on('click', 'form [type="submit"]:not([data-no-spinner])', function() {
            var $btn = $(this);
            var $form = $btn.closest('form');
            var isValid = true;
            if ($form.length && $form.data('validator')) {
                isValid = $form.valid();
            }
            if (isValid) {
                var origHtml = $btn.html();
                $btn.data('orig-html', origHtml);
                setTimeout(function() {
                    if ($btn.is(':visible')) {
                        $btn.prop('disabled', true).html('<span class="wf-spinner"></span> ' + origHtml);
                        setTimeout(function() {
                            $btn.prop('disabled', false).html(origHtml);
                        }, 8000);
                    }
                }, 80);
            }
        });
        $(document).on('invalid-form.validate', 'form', function() {
            var $btn = $(this).find('[type="submit"]');
            var origHtml = $btn.data('orig-html');
            if (origHtml) { $btn.prop('disabled', false).html(origHtml); }
        });

    });
</script>


