@extends('pandit.layouts.app')

@section('styles')
    <!--- Internal Select2 css-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!--  smart photo master css -->
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">PROFILE</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </div>
    </div>
    <!-- /breadcrumb -->

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card custom-card">
                <div class="card-footer py-0">
                    <div class="profile-tab tab-menu-heading border-bottom-0">
                        <nav class="nav main-nav-line p-0 tabs-menu profile-nav-line border-0 br-5 mb-0 full-width-tabs">
                            <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojaskill') }}"
                                onclick="changeColor(this)">Pooja & Expertise</a>
                            <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojadetails') }}"
                                onclick="changeColor(this)">Add Details of Puja</a>
                            <a class="nav-link mb-2 mt-2 active" href="{{ url('pandit/poojalist') }}"
                                onclick="changeColor(this)">Puja Item List</a>
                            <a class="nav-link mb-2 mt-2" href="{{ url('pandit/poojaarea') }}"
                                onclick="changeColor(this)">Areas of Service</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row -->
    @if (session()->has('success'))
        <div class="alert alert-success" id="Message">
            {{ session()->get('success') }}
        </div>
    @endif

    @if ($errors->has('danger'))
        <div class="alert alert-danger" id="Message">
            {{ $errors->first('danger') }}
        </div>
    @endif

    <div class="row mb-5">
        @foreach ($Poojaskills as $pooja)
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card p-3">
                    <div class="card-body">
                        <div class="mb-3 text-center about-team">
                            <!-- Wrap the image inside a label -->
                            <label for="checkbox{{ $pooja->id }}">
                                <img class="rounded-pill" src="{{ asset('assets/img/' . $pooja->pooja_photo) }}"
                                    alt="{{ $pooja->pooja_name }}">
                            </label>
                        </div>
                        <div class="tx-16 text-center font-weight-semibold">
                            {{ $pooja->pooja_name }}
                        </div>
                        <div class="form-check mt-3 text-center">
                            <a href="{{ url('pandit/poojaitem?pooja_id=' . $pooja->id) }}" class="btn btn-primary"
                                data-toggle="tooltip" title="Add Pooja List">+</a>
                            <a style="color: white" class="btn ripple btn-success" data-bs-target="#modaldemo6"
                                data-bs-toggle="modal" data-pooja-id="{{ $pooja->pooja_id }}">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- row closed -->

    {{-- modal start --}}
    <div class="modal fade" id="modaldemo6">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">Pooja Item List</h6>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive export-table">
                        <table id="file-datatable" class="table table-bordered text-nowrap key-buttons border-bottom table-hover">
                            <thead>
                                <tr>
                                    <th style="color: white;background-color: #f74f75;" class="border-bottom-0">Slno</th>
                                    <th style="color: white;background-color: #f74f75;" class="border-bottom-0">Puja Name</th>
                                    <th style="color: white;background-color: #f74f75;" class="border-bottom-0">List Name</th>
                                    <th style="color: white;background-color: #f74f75;" class="border-bottom-0">Quantity</th>
                                   
                                    <th style="color: white;background-color: #f74f75;" class="border-bottom-0">Action</th>
                                </tr>
                            </thead>
                          
                            <tbody>
                                <!-- This will be dynamically filled with AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modaldemo2">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header" style="background-color:#f74f75;color: white">
                    <h6 class="modal-title">Edit Pooja Item</h6>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editItemForm" style="background-color: rgb(239, 227, 227)">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="itemId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="list_name">List Name</label>

                                    <select class="form-control chosen-select" name="list_name" id="list_name" required>
                                        <option value="">Select Puja List</option>
                                        @foreach ($Poojaitemlist as $pujalist)
                                            <option value="{{ $pujalist }}">{{ $pujalist }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="listQuantity">Quantity</label>
                                    <input type="text" class="form-control" id="listQuantity" name="list_quantity" placeholder="Enter Quantity">
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn ripple btn-primary" onclick="submitEditForm();">Update</button>
                        <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div> 

   


@endsection

@section('scripts')
    <!-- Internal Select2 js-->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pandit-poojalist.js') }}"></script> --}}
    <script>

document.addEventListener('DOMContentLoaded', function() {
    var modaldemo6 = document.getElementById('modaldemo6');

    modaldemo6.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var poojaId = button.getAttribute('data-pooja-id'); // Extract info from data-* attributes

        // Store poojaId in the modal element for later use
        modaldemo6.setAttribute('data-pooja-id', poojaId);

        // Fetch and display pooja details in the modal
        fetchPoojaDetails(poojaId);
    });

    modaldemo6.addEventListener('hidden.bs.modal', function () {
        // Ensure backdrop is properly removed
        var backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.parentNode.removeChild(backdrop);
        }
        document.body.classList.remove('modal-open');
        document.body.style = "";
    });
});

function fetchPoojaDetails(poojaId, reopenModal = false) {
    fetch('/pandit/get-poojadetails/' + poojaId)
        .then(response => response.json())
        .then(data => {
            var tableBody = document.querySelector('#modaldemo6 tbody');
            tableBody.innerHTML = ''; // Clear existing table rows

            if (data.error) {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center">${data.error}</td></tr>`;
            } else {
                data.poojaItems.forEach((item, index) => {
                    var row = `<tr id="row-${item.id}">
                        <td>${index + 1}</td>
                        <td>${item.pooja_name}</td>
                        <td>${item.item_name || 'N/A'}</td> <!-- Updated to display item_name -->
                       
                        <td>${item.title || 'N/A'}</td>
                       
                        <td>
                            <button class="btn btn-md btn-danger" onclick="deletePoojaItem(${item.id});"><i class="fa fa-trash"></i></button>
                            <a onclick="openEditModal(${item.id}, '${item.pooja_list}', '${item.list_quantity}', '${item.list_unit}');" class="btn ripple btn-success me-3 edit-item" href="javascript:void(0);">
                                <i class="fa fa-edit"></i>
                            </a>
                        </td>
                    </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            }

            if (reopenModal) {
                var modaldemo6Instance = new bootstrap.Modal(document.getElementById('modaldemo6'));
                modaldemo6Instance.show();
            }
        })
        .catch(error => {
            console.error('Error fetching pooja details:', error);
            var tableBody = document.querySelector('#modaldemo6 tbody');
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center">Error loading data</td></tr>`;
        });
}


function openEditModal(id, poojaList, quantity, unit) {
    var modaldemo2 = new bootstrap.Modal(document.getElementById('modaldemo2'));
    document.getElementById('itemId').value = id;
    document.getElementById('list_name').value = poojaList;
    document.getElementById('listQuantity').value = quantity;
    document.getElementById('weight_unit').value = unit;
    modaldemo2.show();
}

function submitEditForm() {
    var form = document.getElementById('editItemForm');
    var formData = new FormData(form);

    // Get the poojaId from the modal element
    var poojaId = document.getElementById('modaldemo6').getAttribute('data-pooja-id');

    fetch('/pandit/updatepoojalist', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            var modaldemo2 = bootstrap.Modal.getInstance(document.getElementById('modaldemo2'));
            modaldemo2.hide();

            // Ensure backdrop is properly removed
            var backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.parentNode.removeChild(backdrop);
            }
            document.body.classList.remove('modal-open');
            document.body.style = "";

            fetchPoojaDetails(poojaId, true); // Pass true to reopen the main modal
        } else {
            alert(data.error);
        }
    })
    .catch(error => {
        console.error('Error updating pooja item:', error);
        alert('Error updating pooja item.');
    });
}

function deletePoojaItem(itemId) {
    var poojaId = document.getElementById('modaldemo6').getAttribute('data-pooja-id');
    fetch(`/pandit/delete-poojaitem/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Network response was not ok.');
        })
        .then(data => {
            if (data.success) {
                alert(data.success); // Show success message

                // Remove the deleted row from the table
                var deletedRow = document.getElementById('row-' + itemId);
                if (deletedRow) {
                    deletedRow.remove();
                } else {
                    console.error('Row not found in table.');
                }

                // Re-fetch and display the pooja details
                fetchPoojaDetails(poojaId);
            } else {
                throw new Error('Failed to delete item.');
            }
        })
        .catch(error => {
            console.error('Error deleting item:', error);
            alert('Failed to delete item. Please try again.'); // Show error message
        });
}

setTimeout(function() {
    document.getElementById('Message').style.display = 'none';
}, 3000);

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
    </script>
    <!-- smart photo master js -->
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
