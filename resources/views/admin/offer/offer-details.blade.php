@extends('admin.layouts.apps')

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">OFFER DETAILS</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb d-flex justify-content-between align-items-center">
                <li class="breadcrumb-item tx-15"><a href="{{ url('admin/manage-order-assign') }}"
                        class="btn btn-warning text-dark">Manage Offer</a></li>
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item active tx-15" aria-current="page">Offer</li>
            </ol>
        </div>
    </div>

   <div class="card">
    <div class="card-body">
        <form action="{{ route('admin.saveOfferDetails') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="main_header" class="form-label">Main Header</label>
                        <input type="text" class="form-control" id="main_header" name="main_header" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sub_header" class="form-label">Sub Header</label>
                        <input type="text" class="form-control" id="sub_header" name="sub_header">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="discount" class="form-label">Discount (%)</label>
                        <input type="number" class="form-control" id="discount" name="discount" min="0" max="100">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                </div>
              
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>
                </div>

                  <div class="col-md-4">
                    <label class="form-label">Menu Items</label>
                    <div id="menu-container">
                        <div class="input-group mb-2 menu-group">
                            <input type="text" name="menu[]" class="form-control" placeholder="Menu item">
                            <button type="button" class="btn btn-success add-menu">+</button>
                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="form-group">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="4"></textarea>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Save Offer</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JavaScript to handle Add/Remove for menu items -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuContainer = document.getElementById('menu-container');

        menuContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-menu')) {
                const group = document.createElement('div');
                group.classList.add('input-group', 'mb-2', 'menu-group');
                group.innerHTML = `
                    <input type="text" name="menu[]" class="form-control" placeholder="Menu item">
                    <button type="button" class="btn btn-danger remove-menu">−</button>
                `;
                menuContainer.appendChild(group);
            }

            if (e.target.classList.contains('remove-menu')) {
                e.target.closest('.menu-group').remove();
            }
        });
    });
</script>

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6'
        });
    @elseif (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    @endif
</script>

@endsection
