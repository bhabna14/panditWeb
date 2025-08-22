@extends('admin.layouts.apps')

@section('styles')
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .flower-section {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 15px;
    }
    .flower-header {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <span class="main-content-title mg-b-0 mg-b-lg-1">Month-wise Flower Price</span>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <form action="{{ route('admin.saveFlowerPrice') }}" method="POST">
            @csrf

            {{-- Vendor Dropdown --}}
            <div class="form-group mb-3">
                <label for="vendor_id" class="form-label">Select Vendor</label>
                <select class="form-control select2" id="vendor_id" name="vendor_id" required>
                    <option value="">-- Select Vendor --</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Flower Sections --}}
            <div id="flowerSections"></div>

            <button type="submit" class="btn btn-primary mt-3">Save Prices</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let poojaUnits = @json($poojaUnits); // Pass from controller

    $(document).ready(function () {

        // On vendor change fetch flowers
        $('#vendor_id').on('change', function () {
            var vendorId = $(this).val();
            if(vendorId){
                $.ajax({
                    url: "{{ route('admin.getVendorFlowers') }}",
                    type: "GET",
                    data: { vendor_id: vendorId },
                    success: function (res) {
                        let container = $("#flowerSections");
                        container.empty();

                        $.each(res, function (i, flower) {
                            addFlowerSection(flower.product_id, flower.name);
                        });
                    }
                });
            }
        });

        // Function: create flower section
        function addFlowerSection(productId, productName){
            let sectionId = "flower_" + productId;

            let unitOptions = '';
            $.each(poojaUnits, function(i, unit){
                unitOptions += `<option value="${unit.unit_name}">${unit.unit_name}</option>`;
            });

            let section = `
                <div class="flower-section" id="${sectionId}">
                    <div class="flower-header d-flex justify-content-between align-items-center">
                        <span>${productName}</span>
                        <button type="button" class="btn btn-sm btn-success addRow" data-flower="${productId}" data-name="${productName}">+ Add Row</button>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="flower-rows"></tbody>
                    </table>
                </div>
            `;

            $("#flowerSections").append(section);

            // Add default first row
            addRow(productId, productName);
        }

        // Function: add row inside a flower section
        function addRow(productId, productName){
            let uniq = Date.now() + Math.floor(Math.random()*1000);

            let unitOptions = '';
            $.each(poojaUnits, function(i, unit){
                unitOptions += `<option value="${unit.unit_name}">${unit.unit_name}</option>`;
            });

            let row = `
                <tr>
                    <td><input type="date" name="flower[${productId}][${uniq}][from_date]" class="form-control" required></td>
                    <td><input type="date" name="flower[${productId}][${uniq}][to_date]" class="form-control" required></td>
                    <td><input type="number" name="flower[${productId}][${uniq}][quantity]" class="form-control" required></td>
                    <td>
                        <select name="flower[${productId}][${uniq}][unit]" class="form-control" required>
                            <option value="">-- Select Unit --</option>
                            ${unitOptions}
                        </select>
                    </td>
                    <td><input type="number" step="0.01" name="flower[${productId}][${uniq}][price]" class="form-control" required></td>
                    <td>
                        <input type="hidden" name="flower[${productId}][${uniq}][product_id]" value="${productId}">
                        <button type="button" class="btn btn-sm btn-danger removeRow">x</button>
                    </td>
                </tr>
            `;

            $("#flower_" + productId + " .flower-rows").append(row);
        }

        // Event delegation for addRow button
        $(document).on('click', '.addRow', function(){
            let productId = $(this).data('flower');
            let productName = $(this).data('name');
            addRow(productId, productName);
        });

        // Remove row
        $(document).on('click', '.removeRow', function(){
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection
