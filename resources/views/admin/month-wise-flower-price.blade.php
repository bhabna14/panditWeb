@extends('admin.layouts.apps')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .table td, .table th {
            vertical-align: middle;
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

            {{-- Flower List Table --}}
            <div id="flowerTableContainer" style="display:none;">
                <h5>Flower List</h5>
                <table class="table table-bordered" id="flowerTable">
                    <thead>
                        <tr>
                            <th>Flower Name</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th><button type="button" class="btn btn-sm btn-success" id="addRow">+</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Rows will be dynamically appended --}}
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Save Prices</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let poojaUnits = @json($poojaUnits); // Pass from controller

    $(document).ready(function () {
        $('#vendor_id').on('change', function () {
            var vendorId = $(this).val();
            if(vendorId){
                $.ajax({
                    url: "{{ route('admin.getVendorFlowers') }}",
                    type: "GET",
                    data: { vendor_id: vendorId },
                    success: function (res) {
                        $('#flowerTableContainer').show();
                        var tbody = $("#flowerTable tbody");
                        tbody.empty();

                        $.each(res, function (i, flower) {
                            // first row for each flower
                            addRow(flower.product_id, flower.name);
                        });
                    }
                });
            }
        });

        // Add Row button (generic add without flower)
        $('#addRow').click(function(){
            addRow('', '');
        });

        // Add Row Function (supports multiple entries per flower)
        function addRow(productId, productName){
            let uniq = Date.now() + Math.floor(Math.random()*1000);

            let unitOptions = '';
            $.each(poojaUnits, function(i, unit){
                unitOptions += `<option value="${unit.unit_name}">${unit.unit_name}</option>`;
            });

            var row = `<tr>
                <td>
                    <input type="hidden" name="flower[${productId}][${uniq}][product_id]" value="${productId}">
                    <input type="text" class="form-control" value="${productName}" readonly>
                </td>
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
                    <button type="button" class="btn btn-sm btn-danger removeRow">x</button>
                    <button type="button" class="btn btn-sm btn-info duplicateRow">+</button>
                </td>
            </tr>`;
            $("#flowerTable tbody").append(row);
        }

        // Remove Row
        $(document).on('click', '.removeRow', function(){
            $(this).closest('tr').remove();
        });

        // Duplicate Row (same flower)
        $(document).on('click', '.duplicateRow', function(){
            let row = $(this).closest('tr');
            let productId = row.find('input[name*="[product_id]"]').val();
            let productName = row.find('input[type=text]').val();
            addRow(productId, productName);
        });
    });
</script>
@endsection
