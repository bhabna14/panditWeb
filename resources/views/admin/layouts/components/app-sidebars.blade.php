<!-- main-sidebar -->
<div class="sticky">
    <aside class="app-sidebar">
        <div class="main-sidebar-header active">
            <a class="header-logo active" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/brand/Logo_Black.png') }}" class="main-logo desktop-logo" alt="logo">
                <img src="{{ asset('assets/img/brand/logo-white.png') }}" class="main-logo desktop-dark" alt="logo">
                <img src="{{ asset('assets/img/brand/favicon.png') }}" class="main-logo mobile-logo" alt="logo">
                <img src="{{ asset('assets/img/brand/favicon-white.png') }}" class="main-logo mobile-dark"
                    alt="logo">
            </a>
        </div>

        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg>
            </div>

            <ul class="side-menu">
                <li class="side-item side-item-category">Main</li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4zm2-15.586 6 6V15l.001 5H16v-5c0-1.103-.897-2-2-2h-4c-1.103 0-2 .897-2 2v5H6v-9.586l6-6z" />
                        </svg>
                        <span class="side-menu__label">Dashboards</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('flowerDashboard') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M3 13h1v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7h1a1 1 0 0 0 .7-1.7l-9-9a1 1 0 0 0-1.4 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4z" />
                        </svg>
                        <span class="side-menu__label">Flower Dashboard</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />
                        </svg>
                        <span class="side-menu__label">CUSTOMER DETAILS</span>
                        <i class="angle fas fa-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="sub-side-menu__item" href="{{ url('admin/manage-users') }}">Manage User</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('admin.address.categories') }}">Address
                                Summary</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('admin.managelocality') }}">Manage
                                Locality</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 2a5 5 0 0 1 5 5c0 2.5-2.5 7-5 13-2.5-6-5-10.5-5-13a5 5 0 0 1 5-5zm0 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 2a3 3 0 0 1 3 3c0 1.5-1.5 4.5-3 7.5-1.5-3-3-6-3-7.5a3 3 0 0 1 3-3z" />
                        </svg>
                        <span class="side-menu__label">VENDOR DETAILS</span>
                        <i class="angle fas fa-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="sub-side-menu__item" href="{{ route('admin.addVendorDetails') }}">Add Vendor</a>
                        </li>
                        <li><a class="sub-side-menu__item" href="{{ route('admin.managevendor') }}">Manage Vendor</a>
                        </li>
                        <li><a class="sub-side-menu__item" href="{{ route('admin.manageflowerpickupdetails') }}">Manage
                                Flower Pickup</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('admin.monthWiseFlowerPrice') }}">Add Vendor
                                Flower Price</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('admin.manageFlowerPrice') }}">Manage Vendor
                                Flower Price</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M21 7l-1-5H4L3 7H1v2h2l1 12h14l1-12h2V7h-2zm-2 12H5l-1-10h14l-1 10zm-7-9h2v2h-2V10zm0 4h2v2h-2v-2z" />
                        </svg>
                        <span class="side-menu__label">OFFER DETAILS</span>
                        <i class="angle fas fa-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="sub-side-menu__item" href="{{ route('admin.offerDetails')}}">Add Refer Offer</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('admin.manageOfferDetails')}}">Manage Refer Offer</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('refer.offerClaim') }}">Add Offer Claim</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('refer.manageOfferClaim') }}">Manage Offer Claim</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.orders.index') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M20 6h-4V4H8v2H4v2h16V6zm0 4H4v10h16V10zM6 18v-6h2v6H6zm4 0v-6h4v6h-4zm6 0v-6h2v6h-2z" />
                        </svg>
                        <span class="side-menu__label">Manage Flower Orders</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.manageRiderDetails') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5H14V3H6v6h5v2H7c-1.1 0-2 .9-2 2v5h2v-3h2v3h9v-5c0-.64-.24-1.22-.63-1.66l1.54-3.45c.13-.3.15-.64.01-.94z" />
                        </svg>
                        <span class="side-menu__label">Manage Rider</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.manageOrderAssign') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4zM4 12h16v2H4zM4 18h16v2H4z" />
                        </svg>
                        <span class="side-menu__label">Apartment Assign</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ url('admin/manage-delivery-history') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M13 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm0 16c-3.87 0-7-3.13-7-7 0-3.87 3.13-7 7-7 3.87 0 7 3.13 7 7 0 3.87-3.13 7-7 7zm.5-12h-2v6l5.25 3.15.75-1.23-4-2.37V7z" />
                        </svg>
                        <span class="side-menu__label">Delivery History</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.promotionList') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M21 7l-1-5H4L3 7H1v2h2l1 12h14l1-12h2V7h-2zm-2 12H5l-1-10h14l-1 10zm-7-9h2v2h-2V10zm0 4h2v2h-2v-2z" />
                        </svg>
                        <span class="side-menu__label">Flower Promotion</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.officeTransactionDetails') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M2 7c0-1.1.9-2 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7zm2 0v2h16V7H4zm16 4H4v6h16v-6z" />
                        </svg>
                        <span class="side-menu__label">Make Payment</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.officeFundReceived') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M12 21c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9zm1-13h-2v2H8v2h3v2h-2v2h2v2h2v-2h2v-2h-3v-2h2v-2h-2V8z" />
                        </svg>
                        <span class="side-menu__label">Fund Received</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.getFestivalCalendar') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11zm0-13H5V6h14v1z" />
                        </svg>
                        <span class="side-menu__label">Festival Calendar</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M7 18c-1.1 0-2-.9-2-2v-6c0-1.1.   9-2 2-2h10c1.1 0 2 .9 2 2v6c0 1.1-.9 2-2 2H7zM6 6h12v2H6z" />
                        </svg>
                        <span class="side-menu__label">Order Creation</span>
                        <i class="angle fas fa-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="sub-side-menu__item" href="{{ url('admin/existing-user') }}">Subscription Order
                                (Existing User)</a></li>
                        <li><a class="sub-side-menu__item" href="{{ url('admin/new-user-order') }}">Subscription
                                Order (New User)</a></li>
                        <li><a class="sub-side-menu__item" href="{{ url('admin/demo-customize-order') }}">Demo
                                Customize Order</a></li>
                    </ul>
                </li>

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path d="M4 4h16v2H4zm0 4h10v2H4zm0 4h16v2H4zm0 4h10v2H4z" />
                        </svg>
                        <span class="side-menu__label">Marketing</span>
                        <i class="angle fas fa-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="sub-side-menu__item" href="{{ route('admin.followUpSubscriptions') }}">Follow
                                Up</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('admin.getVisitPlace') }}">Visit Place</a>
                        </li>
                    </ul>
                </li>

                @if (session('admin_role') === 'admin')
                    <li class="slide">
                        <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                            <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24">
                                <path d="M10 4v4H4v2h6v4h2V10h6V8h-6V4z" />
                            </svg>
                            <span class="side-menu__label">Product Admin</span>
                            <i class="angle fas fa-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li><a class="sub-side-menu__item"
                                    href="{{ route('admin.productSubscriptionOrder') }}">Manage Order</a></li>
                            <li><a class="sub-side-menu__item" href="{{ route('managePujaList') }}">Manage Items</a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M7 18c-1.1 0-2-.9-2-2v-6c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v6c0 1.1-.9 2-2 2H7zM6 6h12v2H6z" />
                        </svg>
                        <span class="side-menu__label">FINANCE REPORT</span>
                        <i class="angle fas fa-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="sub-side-menu__item" href="{{ route('subscription.report') }}">Subscription
                                Reports</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('report.customize') }}">Customize Flower
                                Reports</a></li>
                        <li><a class="sub-side-menu__item" href="{{ route('report.flower.pickup') }}">Pick-up Flower
                                Reports</a></li>
                    </ul>
                </li>
            </ul>

            <div class="slide-right" id="slide-right">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>

        </div>
    </aside>
</div>
