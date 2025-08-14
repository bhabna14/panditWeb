@extends('admin.layouts.apps')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .benefit-card input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .benefit-card .card {
            transition: box-shadow .15s ease, transform .05s ease, border-color .15s ease;
            border: 1px solid #e6e6e6;
        }

        .benefit-card .card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, .07);
        }

        .benefit-card input[type="checkbox"]:checked+.card {
            border-color: #0d6efd;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        }

        .benefit-card .pair-num {
            font-weight: 600;
        }

        .benefit-toolbar .btn {
            margin-right: .5rem;
        }
    </style>
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">OFFER CLAIM</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ route('refer.manageOfferClaim') }}"
                        class="btn btn-warning text-dark">Manage Offer Claim</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Claim</a></li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('refer.saveOfferClaim') }}" method="POST" enctype="multipart/form-data"
                id="offer-claim-form">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">User</label>
                        <select name="user_id" id="user_id" class="form-select select2" required>
                            <option value="" disabled selected>-- Select User --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->userid }}">
                                    {{ $u->name }} @if ($u->mobile_number)
                                        ({{ $u->mobile_number }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Offer</label>
                        <select name="offer_id" id="offer_id" class="form-select select2" required>
                            <option value="" disabled selected>-- Select Offer --</option>
                            @foreach ($offers as $o)
                                <option value="{{ $o->id }}" data-offer="{{ e($o->offer_name) }}"
                                    data-refer='@json($o->no_of_refer ?? [])' data-benefit='@json($o->benefit ?? [])'>
                                    {{ $o->offer_name }} ({{ ucfirst($o->status ?? 'inactive') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <div id="pairs-wrapper" class="d-none">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-2">Offer Pairs: <span id="pairs-offer-name" class="fw-semibold"></span></h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:80px;">#</th>
                                            <th>No. of Refer</th>
                                            <th>Benefit</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pairs-table-body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

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
                $('.select2').select2({
                    width: '100%'
                });
            }

            // If you want SINGLE select instead of multi, set this to true
            const singleSelect = false;

            const $offer = $('#offer_id');
            const $grid = $('#benefit-grid');

            function cardTemplate(idx, refer, benefit) {
                // value we'll submit; easy to parse server-side if needed: "index|refer|benefit"
                const val = `${idx}|${refer}|${benefit}`;
                return `
            <div class="col-12 col-sm-6 col-lg-4">
                <label class="benefit-card d-block position-relative">
                    <input type="checkbox" class="benefit-check" name="selected_pairs[]" value="${_.escape(val)}"
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

            // Very small underscore-like escape util to avoid XSS in template literals
            window._ = window._ || {};
            _.escape = function(s) {
                if (s === null || s === undefined) return '';
                return String(s)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            // On offer change -> render benefit cards
            $offer.on('change', function() {
                const sel = $(this).find('option:selected');
                const offerName = sel.data('offer') || '';
                let referArr = sel.data('refer') || [];
                let benefitArr = sel.data('benefit') || [];

                // In case they come as JSON strings
                if (typeof referArr === 'string') {
                    try {
                        referArr = JSON.parse(referArr);
                    } catch (e) {}
                }
                if (typeof benefitArr === 'string') {
                    try {
                        benefitArr = JSON.parse(benefitArr);
                    } catch (e) {}
                }

                $('#pairs-offer-name').text(offerName);
                $('#pairs-wrapper').removeClass('d-none');

                $grid.empty();
                const len = Math.min(referArr.length, benefitArr.length);

                if (!len) {
                    $grid.append(
                        '<div class="col-12"><div class="alert alert-secondary mb-0">No benefit options for this offer.</div></div>'
                        );
                    return;
                }

                for (let i = 0; i < len; i++) {
                    $grid.append(cardTemplate(i, referArr[i] ?? '', benefitArr[i] ?? ''));
                }
            });

            // Visual tick icon toggle + single-select behavior
            $(document).on('change', '.benefit-check', function() {
                const $chk = $(this);
                const $card = $chk.next('.card');
                const $icon = $card.find('.check-icon');

                if (singleSelect && $chk.is(':checked')) {
                    // uncheck others
                    $('.benefit-check').not($chk).prop('checked', false).each(function() {
                        $(this).next('.card').find('.check-icon').addClass('d-none');
                    });
                }

                if ($chk.is(':checked')) $icon.removeClass('d-none');
                else $icon.addClass('d-none');
            });

            // Toolbar: Clear & Select All
            $('#benefit-clear').on('click', function() {
                $('.benefit-check').prop('checked', false).trigger('change');
            });

            $('#benefit-select-all').on('click', function() {
                if (singleSelect) {
                    // if single select, just pick the first
                    $('.benefit-check').prop('checked', false).trigger('change');
                    const $first = $('.benefit-check').first();
                    $first.prop('checked', true).trigger('change');
                } else {
                    $('.benefit-check').prop('checked', true).trigger('change');
                }
            });
        });
    </script>
@endsection
