@extends('admin.layouts.apps')

@section('styles')
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <!-- Feather Icons -->
    <link href="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.css" rel="stylesheet">

    <!-- INTERNAL DataTable css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}" rel="stylesheet" />

    <!-- Dashboard custom css -->
    <link href="{{ asset('assets/css/flower-dashboard.css') }}" rel="stylesheet" />

    <!-- Poppins font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --card-radius: 22px;
            --card-border: rgba(148, 163, 184, 0.45);
            --card-shadow: 0 18px 32px rgba(15, 23, 42, 0.14);
            --card-shadow-hover: 0 26px 46px rgba(15, 23, 42, 0.20);
            --card-title: #020617;
            --card-subtitle: #64748b;
            --card-number: #0f172a;

            /* New palette for tables */
            --tbl-head-from: #0ea5e9;
            --tbl-head-to: #6366f1;
            --tbl-head-text: #f9fafb;
            --tbl-row-bg: rgba(248, 250, 252, 0.96);
            --tbl-row-hover: #eff6ff;
            --tbl-border: rgba(148, 163, 184, 0.6);
        }

        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at top, #e0f2fe, #eef2ff 32%, #f9fafb 100%);
        }

        /* ========= Colorful pulse glows (border halo) ========= */
        .pulse-glow--cyan {
            animation: pulseGlowCyan 1.2s ease-in-out 0s 6;
            border-color: rgba(6, 182, 212, .45) !important;
        }

        .pulse-glow--emerald {
            animation: pulseGlowEmerald 1.2s ease-in-out 0s 6;
            border-color: rgba(16, 185, 129, .45) !important;
        }

        .pulse-glow--fuchsia {
            animation: pulseGlowFuchsia 1.2s ease-in-out 0s 6;
            border-color: rgba(217, 70, 239, .45) !important;
        }

        .pulse-glow--amber {
            animation: pulseGlowAmber 1.2s ease-in-out 0s 6;
            border-color: rgba(245, 158, 11, .45) !important;
        }

        @keyframes pulseGlowCyan {
            0% { box-shadow: 0 0 0 rgba(6, 182, 212, 0) }
            50% { box-shadow: 0 0 34px rgba(6, 182, 212, .75) }
            100% { box-shadow: 0 0 0 rgba(6, 182, 212, 0) }
        }

        @keyframes pulseGlowEmerald {
            0% { box-shadow: 0 0 0 rgba(16, 185, 129, 0) }
            50% { box-shadow: 0 0 34px rgba(16, 185, 129, .75) }
            100% { box-shadow: 0 0 0 rgba(16, 185, 129, 0) }
        }

        @keyframes pulseGlowFuchsia {
            0% { box-shadow: 0 0 0 rgba(217, 70, 239, 0) }
            50% { box-shadow: 0 0 34px rgba(217, 70, 239, .75) }
            100% { box-shadow: 0 0 0 rgba(217, 70, 239, 0) }
        }

        @keyframes pulseGlowAmber {
            0% { box-shadow: 0 0 0 rgba(245, 158, 11, 0) }
            50% { box-shadow: 0 0 34px rgba(245, 158, 11, .75) }
            100% { box-shadow: 0 0 0 rgba(245, 158, 11, 0) }
        }

        /* ========= background blink using a pseudo-element ========= */
        .pulse-bg--cyan::after { --tint: rgba(6, 182, 212, .16); animation: pulseBg 1.2s ease-in-out 0s 6; }
        .pulse-bg--emerald::after { --tint: rgba(16, 185, 129, .16); animation: pulseBg 1.2s ease-in-out 0s 6; }
        .pulse-bg--fuchsia::after { --tint: rgba(217, 70, 239, .16); animation: pulseBg 1.2s ease-in-out 0s 6; }
        .pulse-bg--amber::after { --tint: rgba(245, 158, 11, .16); animation: pulseBg 1.2s ease-in-out 0s 6; }

        .pulse-bg--cyan::after,
        .pulse-bg--emerald::after,
        .pulse-bg--fuchsia::after,
        .pulse-bg--amber::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            z-index: 0;
            background: transparent;
        }

        @keyframes pulseBg {
            0%, 100% { background: transparent; }
            50% { background: var(--tint); }
        }

        /* =============== Section wrapper cards ================== */
        .dashboard-section {
            border-radius: var(--card-radius);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(239, 246, 255, 0.98));
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(203, 213, 225, 0.9);
            padding-top: 18px;
            padding-bottom: 18px;
            margin-left: 0;
            margin-right: 0;
        }

        /* Metric tiles inside each section */
        .sales-card,
        .card.sales-card {
            position: relative;
            border-radius: var(--card-radius);
            border: 1px solid var(--card-border);
            /* IMPORTANT: make base transparent so custom bg shows */
            background: transparent !important;
            box-shadow: var(--card-shadow);
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                border-color .18s ease,
                background .18s ease;
            background-clip: padding-box;
            overflow: hidden;
        }

        .sales-card::before,
        .card.sales-card::before {
            content: "";
            position: absolute;
            width: 185px;
            height: 185px;
            right: -70px;
            bottom: -70px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%,
                    rgba(148, 163, 184, 0.22),
                    rgba(148, 163, 184, 0));
            z-index: 0;
        }

        /* remove grey arc on cards where we add strong colors */
        .sub-status-card-ends-today::before,
        .sub-status-card-five-days::before,
        .sub-status-card-renew-pending::before,
        .sub-status-card-assign-rider::before,
        .paused-card-1::before,
        .paused-card-2::before,
        .paused-card-3::before,
        .paused-card-4::before,
        .txn-card-1::before,
        .txn-card-2::before,
        .txn-card-3::before,
        .txn-card-4::before,
        .rider-card-1::before,
        .rider-card-2::before,
        .rider-card-3::before,
        .rider-card-4::before,
        .ref-card-1::before,
        .ref-card-2::before,
        .ref-card-3::before,
        .ref-card-4::before {
            background: none;
        }

        .sales-card > *,
        .card.sales-card > * {
            position: relative;
            z-index: 1;
        }

        .sales-card:hover,
        .card.sales-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
            border-color: rgba(59, 130, 246, 0.95);
        }

        /* --------- EXISTING COLORFUL CARD THEMES ---------- */

        .card-theme-sky {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 40%, #a5f3fc 100%) !important;
        }

        .card-theme-sky::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(59, 130, 246, 0.30),
                    rgba(59, 130, 246, 0));
        }

        .card-theme-indigo {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 40%, #a5b4fc 100%) !important;
        }

        .card-theme-indigo::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(79, 70, 229, 0.34),
                    rgba(79, 70, 229, 0));
        }

        .card-theme-violet {
            background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 40%, #f5d0fe 100%) !important;
        }

        .card-theme-violet::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(139, 92, 246, 0.32),
                    rgba(139, 92, 246, 0));
        }

        .card-theme-rose {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 40%, #ffe4e6 100%) !important;
        }

        .card-theme-rose::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(244, 63, 94, 0.32),
                    rgba(244, 63, 94, 0));
        }

        .card-theme-amber {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 40%, #fed7aa 100%) !important;
        }

        .card-theme-amber::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(245, 158, 11, 0.34),
                    rgba(245, 158, 11, 0));
        }

        .card-theme-emerald {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 40%, #6ee7b7 100%) !important;
        }

        .card-theme-emerald::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(16, 185, 129, 0.32),
                    rgba(16, 185, 129, 0));
        }

        .card-theme-cyan {
            background: linear-gradient(135deg, #ecfeff 0%, #e0f2fe 45%, #a5f3fc 100%) !important;
        }

        .card-theme-cyan::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(6, 182, 212, 0.32),
                    rgba(6, 182, 212, 0));
        }

        .card-theme-lime {
            background: linear-gradient(135deg, #f7fee7 0%, #ecfccb 40%, #bef264 100%) !important;
        }

        .card-theme-lime::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(101, 163, 13, 0.30),
                    rgba(101, 163, 13, 0));
        }

        .card-theme-slate {
            background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 40%, #e0f2fe 100%) !important;
        }

        .card-theme-slate::before {
            background: radial-gradient(circle at 30% 30%,
                    rgba(30, 64, 175, 0.26),
                    rgba(30, 64, 175, 0));
        }

        /* Watch cards extra gradients */
        .watch-card[data-color="amber"] {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 40%, #fed7aa 100%) !important;
        }

        .watch-card[data-color="fuchsia"] {
            background: linear-gradient(135deg, #fdf2ff 0%, #fbcfe8 45%, #e0f2fe 100%) !important;
        }

        .watch-card[data-color="cyan"] {
            background: linear-gradient(135deg, #e0f2fe 0%, #a5f3fc 45%, #eef2ff 100%) !important;
        }

        .watch-card[data-color="emerald"] {
            background: linear-gradient(135deg, #dcfce7 0%, #86efac 45%, #e0f2fe 100%) !important;
        }

        /* Typography */
        .card-title-custom {
            font-weight: 600;
            font-size: 15px;
            color: var(--card-title);
        }

        .sales-card h6,
        .sales-card .tx-12 {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--card-subtitle);
            margin-bottom: 4px;
        }

        .sales-card h4,
        .sales-card .tx-20,
        .sales-card .tx-22 {
            font-weight: 700;
            color: var(--card-number);
            margin-bottom: 0;
        }

        .sales-card h5 {
            font-size: 12px;
            font-weight: 600;
            color: var(--card-subtitle);
        }

        /* --- Sound unlock pill --- */
        #sound-unlock {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 9999;
            background: #020617;
            color: #fff;
            padding: 8px 12px;
            border-radius: 999px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .22);
            font-size: 12px;
            cursor: pointer;
            opacity: .9;
        }

        #sound-unlock.hidden {
            display: none;
        }

        .pulse-day {
            animation-iteration-count: infinite !important;
        }

        .pulse-day::after {
            animation-iteration-count: infinite !important;
        }

        /* =================== COLORFUL TABLE STYLE ===================== */

        .table,
        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0;
            background: var(--tbl-row-bg);
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid var(--tbl-border);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
            margin-bottom: 1rem;
        }

        .table thead:first-child tr:first-child th:first-child,
        table.dataTable thead:first-child tr:first-child th:first-child {
            border-top-left-radius: 18px;
        }

        .table thead:first-child tr:first-child th:last-child,
        table.dataTable thead:first-child tr:first-child th:last-child {
            border-top-right-radius: 18px;
        }

        .table thead th,
        table.dataTable thead th {
            background: linear-gradient(90deg, var(--tbl-head-from), var(--tbl-head-to));
            color: var(--tbl-head-text);
            border-bottom: none !important;
            border-top: none !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
        }

        .table tbody td,
        table.dataTable tbody td {
            border-top: 1px solid rgba(148, 163, 184, 0.3) !important;
            background: var(--tbl-row-bg);
            font-size: 13px;
        }

        .table-striped > tbody > tr:nth-of-type(odd),
        table.dataTable.stripe > tbody > tr:nth-of-type(odd) {
            --bs-table-accent-bg: transparent;
            background: var(--tbl-row-bg);
        }

        .table tbody tr,
        table.dataTable tbody tr {
            background: var(--tbl-row-bg);
            transition: background .18s ease, transform .12s ease, box-shadow .12s ease;
        }

        .table tbody tr:hover,
        table.dataTable tbody tr:hover {
            background: var(--tbl-row-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.10);
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 12px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(90deg, var(--tbl-head-from), var(--tbl-head-to)) !important;
            color: #f9fafb !important;
            border-radius: 999px !important;
            border: none !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 999px !important;
        }

        /* ===================================================== */
        /* CUSTOM COLORS REQUESTED FOR SPECIFIC CARD GROUPS      */
        /* ===================================================== */

        /* Subscription Status – each card different gradient */
        .sub-status-card-ends-today {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 40%, #fecaca 100%) !important;
        }

        .sub-status-card-five-days {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 40%, #fed7aa 100%) !important;
        }

        .sub-status-card-renew-pending {
            background: linear-gradient(135deg, #ecfccb 0%, #bbf7d0 40%, #6ee7b7 100%) !important;
        }

        .sub-status-card-assign-rider {
            background: linear-gradient(135deg, #e0f2fe 0%, #c7d2fe 40%, #a5b4fc 100%) !important;
        }

        /* Paused Subscription – same rgb, different opacity per card */
        .paused-card-1 {
            background: rgba(10, 20, 20, 0.12) !important;
        }

        .paused-card-2 {
            background: rgba(10, 20, 20, 0.18) !important;
        }

        .paused-card-3 {
            background: rgba(10, 20, 20, 0.24) !important;
        }

        .paused-card-4 {
            background: rgba(10, 20, 20, 0.30) !important;
        }

        /* Today's Transaction – same hue, opacity increasing per card */
        .txn-card-1 {
            background: rgba(56, 189, 248, 0.18) !important;
        }

        .txn-card-2 {
            background: rgba(56, 189, 248, 0.24) !important;
        }

        .txn-card-3 {
            background: rgba(56, 189, 248, 0.30) !important;
        }

        .txn-card-4 {
            background: rgba(56, 189, 248, 0.36) !important;
        }

        /* Rider Details (summary) – four soft gradients */
        .rider-card-1 {
            background: linear-gradient(135deg, #eef2ff 0%, #e0f2fe 40%, #bae6fd 100%) !important;
        }

        .rider-card-2 {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 40%, #a7f3d0 100%) !important;
        }

        .rider-card-3 {
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 40%, #fdba74 100%) !important;
        }

        .rider-card-4 {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 40%, #fbcfe8 100%) !important;
        }

        /* Referral Details – same hue, opacity decreasing */
        .ref-card-1 {
            background: rgba(129, 140, 248, 0.36) !important;
        }

        .ref-card-2 {
            background: rgba(129, 140, 248, 0.30) !important;
        }

        .ref-card-3 {
            background: rgba(129, 140, 248, 0.24) !important;
        }

        .ref-card-4 {
            background: rgba(129, 140, 248, 0.18) !important;
        }
    </style>
@endsection

@section('content')
    {{-- TODAY'S ORDER --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Todays Order</h4>
            <div class="row">
                <!-- New Subscription (WATCH) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'new']) }}" target="_blank">
                        <div class="card sales-card watch-card card-theme-amber" data-color="amber">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Subscription</h6>
                                        <h4 id="newUserSubscriptionCount"
                                            data-initial="{{ $newUserSubscription }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $newUserSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Renewed Subscription (WATCH) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'renewed']) }}" target="_blank">
                        <div class="card sales-card watch-card card-theme-violet" data-color="fuchsia">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Renewed Subscription</h6>
                                        <h4 id="renewSubscriptionCount"
                                            data-initial="{{ $renewSubscription }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $renewSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Customize Order (WATCH, main one) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('flower.customize.request', ['filter' => 'today']) }}" target="_blank">
                        <div class="card sales-card watch-card card-theme-cyan" data-color="cyan">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order</h6>
                                        <h4 id="ordersRequestedTodayCount"
                                            data-initial="{{ $ordersRequestedToday }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $ordersRequestedToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Customize Order (Upcoming 3 Days) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('flower.customize.request', ['filter' => 'upcoming']) }}" target="_blank">
                        <div class="card sales-card card-theme-indigo">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Customize Order (Next 3 Days)</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $upcomingCustomizeOrders }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- SUBSCRIPTION STATUS --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Subscription Status</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'end']) }}" target="_blank">
                        <div class="card sales-card sub-status-card-ends-today"  style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 40%, #fecaca 100%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Ends Today</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $todayEndSubscription }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'fivedays']) }}" target="_blank">
                        <div class="card sales-card sub-status-card-five-days"  style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 40%, #eec4c4 90%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Subscription ends in 5 days</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $subscriptionEndFiveDays }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'expired']) }}" target="_blank">
                        <div class="card sales-card sub-status-card-renew-pending" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 40%, #f6d3d3 80%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Subscription Renew Pending</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $expiredSubscriptions }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'rider']) }}" target="_blank">
                        <div class="card sales-card sub-status-card-assign-rider" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 40%, #fbe1e1 70%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">New Order Assign Rider</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $nonAssignedRidersCount }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- PAUSED SUBSCRIPTION --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Paused Subscription</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'todayrequest']) }}" target="_blank">
                        <div class="card sales-card paused-card-1"  style="background: linear-gradient(135deg, #89eda9 0%, #225f01 40%, #91efdd 100%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Paused Request</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $todayPausedRequest }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'paused']) }}" target="_blank">
                        <div class="card sales-card paused-card-2"  style="background: linear-gradient(135deg, #89eda9 0%, #225f01 40%, #91efdd 80%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Paused Subscription</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $pausedSubscriptions }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'tomorrow']) }}" target="_blank">
                        <div class="card sales-card paused-card-3"  style="background: linear-gradient(135deg, #89eda9 0%, #225f01 40%, #91efdd 60%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Next-Day Pause</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $nextDayPaused }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.orders.index', ['filter' => 'nextdayresumed']) }}" target="_blank">
                        <div class="card sales-card paused-card-4"  style="background: linear-gradient(135deg, #89eda9 0%, #225f01 40%, #91efdd 40%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Next-Day Resumed</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $nextDayResumed }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- TODAY'S TRANSACTION --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h6 class="card-title-custom mb-4" style="font-size: 14px">Todays Transaction</h6>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card txn-card-1">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h5 class="mb-2 tx-12">Active Subscription/Total Delivery</h5>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $activeSubscriptions }}/{{ $totalDeliveriesTodayCount }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Today Total Income -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.payments.index') }}" target="_blank">
                        <div class="card sales-card txn-card-2"  style="background: linear-gradient(135deg, #2e026b 0%, #1c69e6 40%, #84b3f1 100%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Total Income</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            ₹{{ number_format($totalIncomeToday, 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Today Total Expenditure -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('flower.expenditure.today') }}" class="text-decoration-none d-block">
                        <div class="card sales-card position-relative txn-card-3" style="background: linear-gradient(135deg, #2e026b 0%, #1c69e6 40%, #84b3f1 80%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Total Expenditure</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            ₹{{ number_format($todayTotalExpenditure, 2) }}
                                        </h4>
                                        <span class="stretched-link"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Tomorrow Active Order -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.tomorrowSubscriptions') }}" target="_blank">
                        <div class="card sales-card txn-card-4" style="background: linear-gradient(135deg, #2e026b 0%, #1c69e6 40%, #84b3f1 60%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h5 class="mb-2 tx-12">Tomorrow Active Order</h5>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $activeTomorrowCount }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- TODAY'S RIDER DETAILS --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12">
            <h4 class="card-title-custom mb-4" style="font-size: 14px">Todays Rider Details</h4>
            <div class="row">
                @foreach ($ridersData as $data)
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                        <a href="{{ route('admin.orderAssign', ['riderId' => $data['rider']->rider_id]) }}"
                           target="_blank" class="text-decoration-none">
                            <div class="sales-card card-theme-slate">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="ps-4 pt-4 pe-3 pb-4">
                                            <h6 class="mb-2 text-dark tx-12">
                                                {{ $data['rider']->rider_name }}
                                            </h6>
                                            <div class="d-flex flex-column">
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-2">
                                                    Delivery Assigned: {{ $data['totalAssignedOrders'] }}
                                                </h4>
                                                <h4 class="tx-12 font-weight-semibold text-dark mb-0">
                                                    Delivered: {{ $data['totalDeliveredToday'] }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- RIDER DETAILS SUMMARY --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12">
            <h4 class="card-title-custom" style="font-size: 14px">Rider Details</h4>
            <div class="row">
                <!-- Total Riders -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.manageRiderDetails') }}" target="_blank">
                        <div class="card sales-card rider-card-1">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Riders</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalRiders }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Delivery Today (WATCH) -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.totalDeliveries') }}" target="_blank">
                        <div class="card sales-card watch-card rider-card-2" data-color="emerald">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Delivery Today</h6>
                                        <h4 id="totalDeliveriesTodayCount"
                                            data-initial="{{ $totalDeliveriesToday }}"
                                            class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalDeliveriesToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Delivery in Month -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.managedeliveryhistory', ['filter' => 'monthlydelivery']) }}"
                       target="_blank">
                        <div class="card sales-card rider-card-3">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Delivery in Month</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalDeliveriesThisMonth }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Delivery -->
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.managedeliveryhistory') }}" target="_blank">
                        <div class="card sales-card rider-card-4">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Delivery</h6>
                                        <h4 class="tx-20 font-weight-semibold mb-2">
                                            {{ $totalDeliveries }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- MARKETING --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Marketing</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.visitPlace', ['filter' => 'todayVisitPlace']) }}" target="_blank">
                        <div class="card sales-card card-theme-rose"  style="background: linear-gradient(135deg, #6b0329 0%, #fa4b9d 40%, #f3afca 100%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Marketing Visit Place Today</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $visitPlaceCountToday }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- REFERRAL DETAILS --}}
    <div class="row mt-2 dashboard-section">
        <div class="col-12 mt-2">
            <h4 class="card-title-custom" style="font-size: 14px">Referal Details</h4>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'claimed', 'date' => 'today']) }}"
                       target="_blank">
                        <div class="card sales-card ref-card-1"  style="background: linear-gradient(135deg, #f5dde5 0%, #fbcad7 40%, #f3afca 100%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Claimed</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $todayClaimed ?? 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('refer.manageOfferClaim', ['status' => 'approved', 'date' => 'today']) }}"
                       target="_blank">
                        <div class="card sales-card ref-card-2"  style="background: linear-gradient(135deg, #f54f8c 0%, #fa4b9d 40%, #f3afca 80%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Approved</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $todayApproved ?? 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.referrals.index', ['date' => 'today']) }}" target="_blank">
                        <div class="card sales-card ref-card-3"  style="background: linear-gradient(135deg, #f54f8c 0%, #fa4b9d 40%, #f3afca 80%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Today Refer</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $todayRefer ?? 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-xs-12 mb-3">
                    <a href="{{ route('admin.referrals.index', ['date' => 'all']) }}" target="_blank">
                        <div class="card sales-card ref-card-4" style="background: linear-gradient(135deg, #f54f8c 0%, #fa4b9d 40%, #f3afca 80%) !important;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ps-4 pt-4 pe-3 pb-4">
                                        <h6 class="mb-2 tx-12">Total Refer</h6>
                                        <h4 class="tx-22 font-weight-semibold mb-2">
                                            {{ $totalRefer ?? 0 }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Add the unlock pill --}}
    <div id="sound-unlock" class="hidden">🔔 Enable sound</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
    <script>
        feather.replace();
    </script>

    <script>
        // Hide welcome sections if present
        setTimeout(() => {
            const el = document.getElementById('welcomeSection');
            if (el) el.style.display = 'none';
        }, 5000);
        setTimeout(() => {
            const el = document.getElementById('welcomeSections');
            if (el) el.style.display = 'none';
        }, 5000);
    </script>

    <!-- Live metrics poll + colorful glow + initial fire + robust sound unlock -->
    <script>
        (function() {
            const watchers = [
                { key: 'ordersRequestedToday', elId: 'ordersRequestedTodayCount', color: 'cyan' },
                { key: 'newUserSubscription', elId: 'newUserSubscriptionCount', color: 'amber' },
                { key: 'renewSubscription', elId: 'renewSubscriptionCount', color: 'fuchsia' },
                { key: 'totalDeliveriesToday', elId: 'totalDeliveriesTodayCount', color: 'emerald' },
            ];

            const els = {}, prev = {};
            watchers.forEach(w => {
                els[w.key] = document.getElementById(w.elId);
                if (els[w.key]) {
                    const initAttr = parseInt(els[w.key].getAttribute('data-initial'), 10);
                    const parsed = Number.isFinite(initAttr) ? initAttr : (parseInt(els[w.key].textContent, 10) || 0);
                    prev[w.key] = parsed;
                }
            });

            function findCard(el) {
                return el ? (el.closest('.watch-card') || el.closest('.card')) : null;
            }

            const DAY_MS = 24 * 60 * 60 * 1000;

            function keyFor(wkey) {
                return `pf_blink_until_${wkey}`;
            }

            function setBlinkUntil(wkey, ts) {
                try { localStorage.setItem(keyFor(wkey), String(ts)); } catch (e) {}
            }

            function getBlinkUntil(wkey) {
                try {
                    const v = localStorage.getItem(keyFor(wkey));
                    return v ? parseInt(v, 10) : 0;
                } catch (e) { return 0; }
            }

            function clearBlinkUntil(wkey) {
                try { localStorage.removeItem(keyFor(wkey)); } catch (e) {}
            }

            function glow(el, color, { persistMs = 0 } = {}) {
                const card = findCard(el);
                if (!card) return;
                const borderCls = `pulse-glow--${color}`;
                const bgCls = `pulse-bg--${color}`;

                card.classList.add(borderCls, bgCls);

                if (persistMs > 0) {
                    card.classList.add('pulse-day');
                    setTimeout(() => {
                        stopGlow(card, borderCls, bgCls, true);
                    }, persistMs);
                } else {
                    setTimeout(() => {
                        stopGlow(card, borderCls, bgCls, false);
                    }, 6000);
                }
            }

            function stopGlow(card, borderCls, bgCls, removePulseDay) {
                if (!card.classList.contains('pulse-day')) {
                    card.classList.remove(borderCls, bgCls);
                }
                if (removePulseDay) {
                    card.classList.remove('pulse-day', borderCls, bgCls);
                }
            }

            // ---- audio (unlock + queue + throttle) ----
            let audioEnabled = false, audioCtx = null;
            const beepQueue = [];
            let lastBeepAt = 0;
            const BEEP_COOLDOWN_MS = 3500;

            function ensureAudio() {
                try {
                    if (!audioCtx) audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                    if (audioCtx && audioCtx.state === 'suspended') {
                        audioCtx.resume().then(() => {
                            audioEnabled = true;
                            flushBeepQueue();
                        });
                    } else {
                        audioEnabled = true;
                        flushBeepQueue();
                    }
                } catch (e) {}
            }

            function flushBeepQueue() {
                while (audioEnabled && beepQueue.length) {
                    const [ms, f] = beepQueue.shift();
                    _beep(ms, f);
                }
            }

            function _beep(ms = 230, freq = 880) {
                try {
                    const now = Date.now();
                    if (now - lastBeepAt < BEEP_COOLDOWN_MS) return;
                    lastBeepAt = now;
                    const osc = audioCtx.createOscillator(), gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = freq;
                    gain.gain.value = 0.0001;
                    osc.connect(gain).connect(audioCtx.destination);
                    osc.start();
                    gain.gain.exponentialRampToValueAtTime(0.07, audioCtx.currentTime + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + (ms / 1000));
                    setTimeout(() => {
                        try { osc.stop(); } catch (e) {}
                    }, ms + 60);
                } catch (e) {}
            }

            function beep(ms = 230, f = 880) {
                if (!audioEnabled) {
                    beepQueue.push([ms, f]);
                    return;
                }
                _beep(ms, f);
            }

            function setupUnlockUI() {
                const pill = document.getElementById('sound-unlock');
                const hide = () => pill && pill.classList.add('hidden');
                if (audioEnabled) { hide(); return; }
                if (pill) pill.classList.remove('hidden');
                const unlock = () => {
                    ensureAudio();
                    hide();
                    window.removeEventListener('click', unlock, true);
                    window.removeEventListener('keydown', unlock, true);
                    pill && pill.removeEventListener('click', unlock, true);
                };
                window.addEventListener('click', unlock, true);
                window.addEventListener('keydown', unlock, true);
                pill && pill.addEventListener('click', unlock, true);
            }

            function initialKick() {
                watchers.forEach(w => {
                    const el = els[w.key];
                    if (!el) return;
                    const val = prev[w.key] ?? 0;

                    const until = getBlinkUntil(w.key);
                    const dayActive = until && Date.now() < until;

                    if (dayActive) {
                        glow(el, w.color, { persistMs: until - Date.now() });
                    }

                    if (!dayActive && val > 0) {
                        glow(el, w.color);
                        if (w.key === 'ordersRequestedToday') {
                            beep(260, 1200);
                            setTimeout(() => beep(220, 900), 160);
                        } else {
                            beep(200, 880);
                        }
                    }

                    if (!dayActive && until && Date.now() >= until) clearBlinkUntil(w.key);
                });
            }

            async function poll() {
                try {
                    const url = `{{ route('admin.flowerDashboard.liveMetrics') }}`;
                    const res = await fetch(url, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store'
                    });
                    if (!res.ok) throw new Error('Bad response');
                    const json = await res.json();
                    if (!json || !json.ok || !json.data) return;
                    const data = json.data;

                    watchers.forEach(w => {
                        const el = els[w.key];
                        if (!el) return;
                        const newVal = parseInt(data[w.key], 10) || 0;
                        const oldVal = prev[w.key] ?? 0;

                        if (newVal !== oldVal) {
                            el.textContent = newVal;

                            if (newVal > oldVal) {
                                if (w.key === 'ordersRequestedToday') {
                                    const untilTs = Date.now() + DAY_MS;
                                    setBlinkUntil(w.key, untilTs);
                                    glow(el, w.color, { persistMs: DAY_MS });
                                    beep(300, 1250);
                                    setTimeout(() => beep(240, 920), 170);
                                } else {
                                    glow(el, w.color);
                                    beep(200, 880);
                                }
                            }
                            prev[w.key] = newVal;
                        }
                    });
                } catch (e) {
                    // silent
                }
            }

            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') poll();
            });
            document.addEventListener('DOMContentLoaded', () => {
                setupUnlockUI();
                initialKick();
                poll();
                setInterval(poll, 5000);
            });
        })();
    </script>
@endsection
