@extends('admin.layouts.apps')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
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

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Claim</button>
                        <a href="{{ route('refer.manage') }}" class="btn btn-light">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // enhance selects
            if (window.jQuery && $.fn.select2) {
                $('.select2').select2({
                    width: '100%'
                });
            }

            // On offer change => render pairs
            $('#offer_id').on('change', function() {
                const sel = $(this).find('option:selected');
                const offerName = sel.data('offer') || '';
                let referArr = sel.data('refer') || [];
                let benefitArr = sel.data('benefit') || [];

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
                const tbody = $('#pairs-table-body').empty();
                const len = Math.min(referArr.length, benefitArr.length);

                if (!len) {
                    tbody.append(
                        '<tr><td colspan="3" class="text-center text-muted">No pairs found.</td></tr>');
                } else {
                    for (let i = 0; i < len; i++) {
                        const idx = i + 1;
                        const r = referArr[i] ?? '';
                        const b = benefitArr[i] ?? '';
                        tbody.append(`<tr><td>${idx}</td><td>${r}</td><td>${b}</td></tr>`);
                    }
                }
                $('#pairs-wrapper').removeClass('d-none');
            });
        });
    </script>
@endsection
