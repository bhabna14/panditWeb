@extends('admin.layouts.apps')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        :root{
            --brand:#4f46e5;       /* indigo */
            --brand-2:#06b6d4;     /* cyan */
            --ink:#0f172a;         /* slate-900 */
            --muted:#667085;       /* slate-500 */
            --line:#eef2f7;
            --soft:#f8fafc;
        }
        .card.premium{
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }
        .card-header.premium{
            background: linear-gradient(180deg, #fff, #fbfcff);
            border-bottom: 1px solid var(--line);
        }
        .subtitle{ color: var(--muted); font-size: .875rem; }

        /* Select2 */
        .select2-container--default .select2-selection--single{
            border: 1px solid #e6e6e6; height: 38px; border-radius: 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            line-height: 38px; padding-left: 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            height: 38px; right: 6px;
        }

        /* Benefit grid */
        .toolbar{
            background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 10px;
        }
        .toolbar .form-control{
            border-radius: 10px;
        }
        .btn-brand{
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            border: none; color: #fff;
            box-shadow: 0 10px 20px rgba(79,70,229,.25);
        }
        .btn-brand:hover{ opacity:.95 }
        .btn-outline-brand{
            border-color: var(--brand);
            color: var(--brand);
        }
        .btn-outline-brand:hover{
            background: #eef3ff;
            border-color: var(--brand);
            color: var(--brand);
        }

        .benefit-card input[type="checkbox"]{ position:absolute; opacity:0; pointer-events:none; }
        .benefit-card .card{
            transition: box-shadow .15s ease, transform .05s ease, border-color .15s ease, background .2s ease;
            border: 1px solid #e6e6e6; border-radius: 14px;
        }
        .benefit-card .card:hover{ box-shadow: 0 6px 16px rgba(0,0,0,.07); }
        .benefit-card input[type="checkbox"]:checked + .card{
            border-color: var(--brand);
            box-shadow: 0 0 0 .18rem rgba(79,70,229,.15);
            background: #fbfdff;
        }
        .pair-num{ font-weight: 700; font-variant-numeric: tabular-nums; }
        .check-icon{ transition: .15s ease; }
        .tag{
            display:inline-flex; align-items:center; gap:6px; padding:.25rem .5rem; border-radius:999px;
            background:#eef3ff; color:var(--brand); border:1px solid rgba(79,70,229,.25); font-weight:600; font-size:.85rem;
        }

        /* Selection summary */
        .summary{
            background:#fff; border:1px solid var(--line); border-radius:12px; padding:10px;
        }
        .summary .badge{
            background:#f6f9ff; color:#1f2b6b; border:1px solid #d9e4ff;
        }

        /* Required hint */
        .help{ font-size:.85rem; color:var(--muted); }
    </style>
@endsection

@section('content')
    <div class="card premium">
        <div class="card-header premium d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">Claim Offer</h5>
                <div class="subtitle">Pick a user, choose an offer, and select one or more benefit options to claim.</div>
            </div>
            <span class="badge tag">
                <i class="bi bi-gift"></i> Referral · Benefits
            </span>
        </div>

        <div class="card-body">
            {{-- Quick preview chips --}}
            <div class="mb-3 d-flex flex-wrap gap-2">
                <span class="tag"><i class="bi bi-person-check"></i> <span id="chip-user">No user</span></span>
                <span class="tag"><i class="bi bi-card-text"></i> <span id="chip-offer">No offer</span></span>
                <span class="tag"><i class="bi bi-calendar3"></i> <span id="chip-dt">No date</span></span>
                <span class="tag"><i class="bi bi-check2-square"></i> <span id="chip-selected">0 selected</span></span>
            </div>

            <form action="{{ route('refer.saveOfferClaim') }}" method="POST" enctype="multipart/form-data" id="offer-claim-form" novalidate>
                @csrf

                {{-- Basic fields --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">User</label>
                        <select name="user_id" id="user_id" class="form-select select2" required>
                            <option value="" disabled selected>-- Select User --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->userid }}">
                                    {{ $u->name }} @if ($u->mobile_number) ({{ $u->mobile_number }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="help mt-1">Start typing to find a user quickly.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Claim Date &amp; Time</label>
                        <input type="datetime-local" name="claim_datetime" id="claim_datetime" class="form-control" required>
                        <div class="help mt-1">Defaults to now if left empty.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Offer</label>
                        <select name="offer_id" id="offer_id" class="form-select select2" required>
                            <option value="" disabled selected>-- Select Offer --</option>
                            @foreach ($offers as $o)
                                <option value="{{ $o->offer_id }}"
                                        data-offer="{{ e($o->offer_name) }}"
                                        data-refer='@json($o->no_of_refer ?? [])'
                                        data-benefit='@json($o->benefit ?? [])'>
                                    {{ $o->offer_name }} ({{ ucfirst($o->status ?? 'inactive') }})
                                </option>
                            @endforeach
                        </select>
                        <div class="help mt-1">Selecting an offer reveals its benefit options below.</div>
                    </div>
                </div>

                {{-- Benefit grid + toolbar --}}
                <div class="mt-4 d-none" id="pairs-wrapper">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Benefit Options for: <span id="pairs-offer-name" class="fw-semibold"></span></h6>

                        <div class="toolbar d-flex flex-wrap align-items-center gap-2">
                            <div class="input-group input-group-sm" style="min-width:260px;">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" id="benefit-search" class="form-control" placeholder="Search benefit or refer…">
                            </div>

                            <div class="form-check form-switch ms-1">
                                <input class="form-check-input" type="checkbox" id="toggle-single">
                                <label class="form-check-label small" for="toggle-single">Single select</label>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-secondary" id="benefit-clear">
                                <i class="bi bi-x-circle me-1"></i> Clear
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="benefit-select-all">
                                <i class="bi bi-check2-all me-1"></i> Select All
                            </button>
                        </div>
                    </div>

                    <div id="benefit-grid" class="row g-3"><!-- cards injected by JS --></div>

                    {{-- Selection summary --}}
                    <div class="summary mt-3 d-flex flex-wrap align-items-center gap-2">
                        <div><strong>Selected:</strong> <span id="selected-count">0</span></div>
                        <div class="vr mx-2 d-none d-sm-block"></div>
                        <div id="selected-list" class="d-flex flex-wrap gap-1"></div>
                    </div>

                    <small class="text-muted d-block mt-2">Tip: You can select multiple options (unless single-select is on).</small>
                </div>

                {{-- Submit --}}
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-brand px-4">
                        <i class="bi bi-save me-1"></i> Save Claim
                    </button>
                    <button type="reset" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhance selects
            if (window.jQuery && $.fn.select2) {
                $('.select2').select2({ width: '100%' });
            }

            // Chips live preview
            const chipUser = document.getElementById('chip-user');
            const chipOffer = document.getElementById('chip-offer');
            const chipDt = document.getElementById('chip-dt');
            const chipSel = document.getElementById('chip-selected');

            const userSel = document.getElementById('user_id');
            const dtSel = document.getElementById('claim_datetime');
            const offerSel = document.getElementById('offer_id');

            function syncChips(){
                const uText = userSel.options[userSel.selectedIndex]?.text?.trim() || 'No user';
                const oText = offerSel.options[offerSel.selectedIndex]?.text?.trim() || 'No offer';
                const dtVal = dtSel.value ? new Date(dtSel.value).toLocaleString('en-IN') : 'No date';
                chipUser.textContent = uText;
                chipOffer.textContent = oText;
                chipDt.textContent = dtVal;
            }
            userSel.addEventListener('change', syncChips);
            offerSel.addEventListener('change', syncChips);
            dtSel.addEventListener('change', syncChips);

            // Benefit grid
            const $offer = $('#offer_id');
            const $grid = $('#benefit-grid');
            const $pairsWrap = $('#pairs-wrapper');
            const $search = $('#benefit-search');
            const $selectedCount = $('#selected-count');
            const $selectedList = $('#selected-list');
            const $chipSelected = $('#chip-selected');
            const $toggleSingle = $('#toggle-single');

            // tiny escape util
            window._ = window._ || {};
            _.escape = function(s) {
                if (s === null || s === undefined) return '';
                return String(s).replace(/&/g, '&amp;')
                                .replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;')
                                .replace(/"/g, '&quot;')
                                .replace(/'/g, '&#039;');
            };

            function cardTemplate(idx, refer, benefit) {
                const val = `${idx}|${refer}|${benefit}`;
                return `
                    <div class="col-12 col-sm-6 col-lg-4 benefit-col" data-filter="${_.escape((refer+' '+benefit).toLowerCase())}">
                        <label class="benefit-card d-block position-relative w-100">
                            <input type="checkbox" class="benefit-check" name="selected_pairs[]"
                                   value="${_.escape(val)}"
                                   data-index="${idx}" data-refer="${_.escape(refer)}" data-benefit="${_.escape(benefit)}">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-dark">#<span class="pair-num">${idx + 1}</span></span>
                                        <i class="bi bi-check2-circle fs-5 text-success d-none check-icon"></i>
                                    </div>
                                    <div class="mt-3">
                                        <div class="text-muted small">No. of Refer</div>
                                        <div class="fs-5 fw-semibold">${_.escape(refer)}</div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="text-muted small">Benefit</div>
                                        <div class="fs-6">${_.escape(benefit)}</div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                `;
            }

            let dataset = []; // [{i, refer, benefit}]
            function renderGrid(){
                $grid.empty();
                if (!dataset.length) {
                    $grid.append(
                        '<div class="col-12"><div class="alert alert-secondary mb-0">No benefit options for this offer.</div></div>'
                    );
                    return;
                }
                dataset.forEach((x, i)=> $grid.append(cardTemplate(i, x.refer, x.benefit)));
                applySearch(); // respect current query
            }

            function applySearch(){
                const q = ($search.val() || '').toLowerCase().trim();
                if (!q){
                    $grid.find('.benefit-col').removeClass('d-none');
                    return;
                }
                $grid.find('.benefit-col').each(function(){
                    const hay = this.getAttribute('data-filter');
                    if (hay.includes(q)){ this.classList.remove('d-none'); }
                    else { this.classList.add('d-none'); }
                });
            }

            function updateSummary(){
                const checked = $('.benefit-check:checked');
                const count = checked.length;
                $selectedCount.text(count);
                $chipSelected.text(`${count} selected`);

                $selectedList.empty();
                checked.each(function(){
                    const idx = Number(this.getAttribute('data-index')) + 1;
                    const ref = this.getAttribute('data-refer');
                    const ben = this.getAttribute('data-benefit');
                    const badge = document.createElement('span');
                    badge.className = 'badge';
                    badge.textContent = `#${idx}: ${ref} → ${ben}`;
                    $selectedList.append(badge);
                });
            }

            // On offer change -> hydrate dataset & render
            $offer.on('change', function() {
                const sel = $(this).find('option:selected');
                const offerName = sel.data('offer') || '';
                let referArr = sel.data('refer') || [];
                let benefitArr = sel.data('benefit') || [];

                if (typeof referArr === 'string') { try { referArr = JSON.parse(referArr); } catch (e) {} }
                if (typeof benefitArr === 'string') { try { benefitArr = JSON.parse(benefitArr); } catch (e) {} }

                $('#pairs-offer-name').text(offerName);
                $pairsWrap.removeClass('d-none');

                // build dataset
                const len = Math.min(referArr.length || 0, benefitArr.length || 0);
                dataset = [];
                for (let i = 0; i < len; i++) {
                    dataset.push({ i, refer: referArr[i] ?? '', benefit: benefitArr[i] ?? '' });
                }

                renderGrid();
                updateSummary();
            });

            // Visual tick + single-select behavior
            $(document).on('change', '.benefit-check', function() {
                const $chk = $(this);
                const $card = $chk.next('.card');
                const $icon = $card.find('.check-icon');

                if ($toggleSingle.is(':checked') && $chk.is(':checked')) {
                    $('.benefit-check').not($chk).prop('checked', false).each(function() {
                        $(this).next('.card').find('.check-icon').addClass('d-none');
                    });
                }
                if ($chk.is(':checked')) $icon.removeClass('d-none');
                else $icon.addClass('d-none');

                updateSummary();
            });

            // Toolbar actions
            $('#benefit-clear').on('click', function() {
                $('.benefit-check').prop('checked', false).trigger('change');
            });
            $('#benefit-select-all').on('click', function() {
                if ($toggleSingle.is(':checked')) {
                    $('.benefit-check').prop('checked', false).trigger('change');
                    const $first = $('.benefit-check:visible').first();
                    $first.prop('checked', true).trigger('change');
                } else {
                    $('.benefit-check:visible').prop('checked', true).trigger('change');
                }
            });
            $search.on('input', applySearch);

            // Default date-time: now
            const dt = document.getElementById('claim_datetime');
            if (!dt.value) {
                const now = new Date();
                const pad = n => String(n).padStart(2,'0');
                dt.value = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
                syncChips();
            }

            // Form validation guard
            document.getElementById('offer-claim-form').addEventListener('submit', function(e){
                const anyChecked = document.querySelectorAll('.benefit-check:checked').length > 0;
                if (!anyChecked) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pick at least one benefit',
                        text: 'Please select one or more benefit options before saving the claim.'
                    });
                }
            });

            // Flash/validation popups (server-side)
            @if ($errors->any())
                (function() {
                    const fieldErrors = @json($errors->toArray());
                    let html = '';
                    Object.entries(fieldErrors).forEach(([field, msgs]) => {
                        (msgs || []).forEach(msg => {
                            html += `<div><strong>${field}</strong>: ${msg}</div>`;
                        });
                    });
                    Swal.fire({ icon: 'error', title: 'Please fix the following', html });
                })();
            @elseif (session('error'))
                (function() {
                    const msg = @json(session('error'));
                    const detail = @json(session('error_detail'));
                    let html = `<div>${msg}</div>`;
                    if (detail) {
                        html += `<pre style="text-align:left; white-space:pre-wrap; margin-top:8px;">${detail}</pre>`;
                    }
                    Swal.fire({ icon: 'error', title: 'Error', html });
                })();
            @elseif (session('success'))
                Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')), timer: 1800, showConfirmButton: false });
            @endif
        });
    </script>
@endsection
