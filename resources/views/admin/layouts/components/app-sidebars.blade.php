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
                    <a class="side-menu__item" href="{{ url('admin/manage-users') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zM8 11c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V20h14v-3.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05C15.67 14.17 18 15.17 18 16.5V20h4v-3.5c0-2.33-4.67-3.5-6-3.5z" />
                        </svg>
                        <span class="side-menu__label">Manage Users</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.managelocality') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                        </svg>
                        <span class="side-menu__label">Manage Locality</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.orders.index') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M20 6h-4V4H8v2H4v2h16V6zm0 4H4v10h16V10zM6 18v-6h2v6H6zm4 0v-6h4v6h-4zm6 0v-6h2v6h-2z" />
                        </svg>
                        <span class="side-menu__label">Manage Flower Orders</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.managevendor') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path d="M3 4v16h18V4H3zm16 14H5V6h14v12zM8 8h2v2H8V8zm4 0h4v2h-4V8z" />
                        </svg>
                        <span class="side-menu__label">Manage Vendors</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.manageRiderDetails') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path
                                d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5H14V3H6v6h5v2H7c-1.1 0-2 .9-2 2v5h2v-3h2v3h9v-5c0-.64-.24-1.22-.63-1.66l1.54-3.45c.13-.3.15-.64.01-.94z" />
                        </svg>
                        <span class="side-menu__label">Manage Rider</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.manageOrderAssign') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24">
                            <path d="M4 6h16v2H4zM4 12h16v2H4zM4 18h16v2H4z" />
                        </svg>
                        <span class="side-menu__label">Apartment Assign</span>
                    </a>
                </li>

                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.manageflowerpickupdetails') }}">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M20 8h-3V6c0-1.1-.9-2-2-2H5C3.9 4 3 4.9 3 6v9h2c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2h6c0 1.1.9 2 2 2h1c1.1 0 2-.9 2-2V9c0-.55-.45-1-1-1z" />
                        </svg>
                        <span class="side-menu__label">Manage Flower Pickup</span>
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
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                        <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24">
                            <path
                                d="M7 18c-1.1 0-2-.9-2-2v-6c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2v6c0 1.1-.9 2-2 2H7zM6 6h12v2H6z" />
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
                    </ul>
                </li>

                  <li class="slide">
                    <a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
                    <svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/>
                    </svg>
                        <span class="side-menu__label">Address Summary</span>
                        <i class="angle fas fa-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a class="sub-side-menu__item" href="{{ route('admin.address.categories') }}">Follow
                                Up</a></li>
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
                        </ul>
                    </li>
                @endif
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
