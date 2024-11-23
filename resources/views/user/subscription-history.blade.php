@extends('user.layouts.front-flower-dashboard')


@section('styles')
<style>
  .rejected-status{
    margin-bottom: 20px;
  }
  .rejected-status a{
    color: blue;
    font-weight: bold;
    text-decoration: underline;
  }
  .rejected-text{
    margin-bottom: 20px;
  }
  .order-history-sec .status-text a {
    pointer-events: auto;
  }
  .filter-buttons a {
    margin-right: 10px;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    color: #c80100;
    border: 1px solid;
}
  .filter-buttons a.active {
    background-color: #c80100;
    color:#fff;
  }
  .filter-buttons  a.active a:hover{
    color: #fff !important;
  }
  .refund-details {
    padding: 10px;
    border: 1px solid #ddd;
    margin: 10px 12px 15px 12px;
    font-weight: 500;
}
/* Styling for the 'Active' badge */
.badge-success {
    background-color: #008009 ; /* Green color for active */
    color: white;
    font-weight: bold;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}
.badge-warning {
    background-color: #c80100 ; /* Green color for active */
    color: white;
    font-weight: bold;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}
.highlighted-text.mt-2 {
    background-color: #ffb837;
    padding: 6px 10px;
    border-radius: 9px;
}

/* Styling for the 'Not Started' badge */
.badge-secondary {
    background-color: #6c757d; /* Gray color for not started */
    color: white;
    font-weight: bold;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

/* Optional: Add some spacing between the badge and the content */
.text-right .badge {
    margin-top: 5px;
}

    /* Modal styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
    }

    .modal-content {
        position: relative;
        background-color: #fff;
        padding: 20px;
        margin: 10% auto;
        width: 50%;
        max-width: 500px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 28px;
        font-weight: bold;
        color: #aaa;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .modal form {
        display: flex;
        flex-direction: column;
    }

    .modal form div {
        margin-bottom: 15px;
    }

    .modal form label {
        font-weight: bold;
    }

    .modal form input[type="date"] {
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .modal .btn {
        background-color: #c80100;
        color: white;
        padding: 10px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        border-radius: 4px;
        margin-top: 10px;
    }

    .modal .btn:hover {
        background-color: #ff5733;
    }

    .text-muted {
        font-size: 12px;
        color: #888;
    }
    .text-strike {
    text-decoration: line-through;
    color: #a5a5a5; /* Optional: Grey color for struck-through text */
    margin-right: 10px;
}

.text-highlight {
    font-weight: bold;
    color: #2e7d32; /* Optional: Green for emphasis */
}


</style>
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content bg-light-2">
    <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
      <div class="col-auto">
        <h1 class="text-30 lh-14 fw-600">Booking History</h1>
      </div>
      
    </div>
    <div class="row  y-gap-20 justify-between items-end pb-30 lg:pb-40 md:pb-32">
      <div class="col-auto">
        <div class="filter-buttons">
          <a href="{{ route('subscription.history') }}" 
          class="{{ request()->routeIs('subscription.history') ? 'active' : '' }}">
          Subscription History
       </a>
       
       <a href="{{ route('requested.order.history') }}" 
          class="{{ request()->routeIs('requested.order.history') ? 'active' : '' }}">
          Customized Order History
       </a>
       
        </div>
      </div>
    </div>

    <div class="row">
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

      @forelse ($subscriptionsOrder as $order)

      <div class="col-md-12">
            <div class="order-history-sec">
                <div class="order-details">
                    <div class="row">
                        <div class="col-md-3">
                            SUBSCRIPTION START DATE <br>
                            {{ \Carbon\Carbon::parse($order->subscription->start_date)->format('Y-m-d') }} <!-- Subscription start date -->
                        </div>
                        <div class="col-md-3">
                            SUBSCRIPTION END DATE <br>
                            @if ($order->subscription->new_date)
                                <span class="text-strike">
                                    {{ \Carbon\Carbon::parse($order->subscription->end_date)->format('Y-m-d') }}
                                </span>
                                <span class="text-highlight ms-2">
                                    {{ \Carbon\Carbon::parse($order->subscription->new_date)->format('Y-m-d') }}
                                </span>
                            @else
                                {{ \Carbon\Carbon::parse($order->subscription->end_date)->format('Y-m-d') }}
                            @endif
                        </div>
                        
                        <div class="col-md-2">
                            TOTAL PAYMENT <br>
                            ₹ {{ number_format($order->total_price), 2 }} <!-- Total payment from flowerPayments -->
                        </div>
                        <div class="col-md-2 text-right">
                            ORDER NUMBER <br>
                            <span style="font-size: 15px">#{{ $order->order_id }}</span> <!-- Order number -->
                        </div>
                        <div class="col-md-2 text-center" style="    margin-top: 15px;">
                          <!-- Check if today's date matches the start date -->
                          {{-- @if (\Carbon\Carbon::today()->toDateString() === \Carbon\Carbon::parse($order->subscription->start_date)->toDateString()) --}}
                              <!-- Display the success badge if the dates match -->
                        <span class="badge 
                              {{ $order->subscription->status === 'paused' ? 'badge-warning' : 'badge-success' }}">
                              {{ ucfirst($order->subscription->status) }}
                          </span>
                          {{-- @else --}}
                              <!-- Display the "Not Started" badge if the dates don't match -->
                              {{-- <span class="badge badge-secondary">Not Started</span> --}}
                          {{-- @endif --}}
                      </div>
                      
                    </div>
                </div>
                <div class="row order-details-booking">
                    <div class="col-md-2">
                        <img src="{{ $order->flowerProduct->product_image_url }}" alt="Product Image" /> <!-- Display product image -->
                    </div>
                    <div class="col-md-7">
                        <h6>{{ $order->flowerProduct->name }}</h6> <!-- Subscription name -->
                        <p>{{ $order->flowerProduct->description }}</p> <!-- Subscription description -->
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('subscription.details', ['order_id' => $order->order_id]) }}" class="button px-10 fw-400 text-14 pay-button-bg h-50 text-white">
                            View Details
                         </a>
                         
                        
                                            
                        @if ($order->subscription->status == "active")
                            <a href="javascript:void(0);" 
                            class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white pause-button" 
                            style="margin-bottom: 10px; background-color: #c80100 !important;" 
                            onclick="openPauseModal('{{ $order->subscription->start_date }}', '{{ $order->subscription->end_date }}')">
                            Pause
                            </a>
                        @elseif ($order->subscription->status == "paused")
                            <a href="javascript:void(0);" 
                            class="button px-10 fw-400 text-14 bg-dark-4 h-50 text-white resume-button" 
                            style="margin-bottom: 10px; background-color: #c80100 !important;" 
                            onclick="openResumeModal('{{ $order->subscription->pause_start_date }}', '{{ $order->subscription->pause_end_date }}')">
                            Resume
                            </a>
                        @endif
                    
                        <!-- Pause Modal -->
                        <div id="pauseModal" class="modal">
                        <div class="modal-content">
                            <span class="close" onclick="closePauseModal()">&times;</span>
                            <h2>Pause Subscription</h2>
                            <form id="pauseForm">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                <div>
                                    <label for="pause_start_date">Pause Start Date</label>
                                    <input type="date" id="pause_start_date" name="pause_start_date" required>
                                </div>
                                <div>
                                    <label for="pause_end_date">Pause End Date</label>
                                    <input type="date" id="pause_end_date" name="pause_end_date" required>
                                </div>
                                <p class="text-muted">
                                    Dates must be between <span id="subscriptionStart"></span> and <span id="subscriptionEnd"></span>.
                                </p>
                                <button type="submit" class="btn">Submit</button>
                            </form>
                        </div>
                        </div>

                        <!--- Resume Modal -->
                       <!-- Resume Modal -->
                        <div id="resumeModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <span class="close" onclick="closeModal('resumeModal')">&times;</span>
                                <h2>Resume Subscription</h2>
                                <form id="resumeForm">
                                    @csrf
                                    <!-- Resume Date Input -->
                                    <label for="resume_date">Select Resume Date:</label>
                                    <input type="date" id="resume_date" name="resume_date" required>
                            
                                    <!-- Hidden Order ID -->
                                    <input type="hidden" name="order_id" id="order_id" value="{{ $order->order_id }}">
                                    <button type="submit" class="btn">Resume</button>
                                </form>
                            </div>
                        </div>


                        


                    </div>
                </div>
                @if ($order->subscription->status === 'paused')
                <div class="highlighted-text mt-2">
                    <strong>Note:</strong> Your subscription is paused from 
                    <span class="text-highlight">{{ \Carbon\Carbon::parse($order->subscription->pause_start_date)->format('Y-m-d') }}</span> 
                    to 
                    <span class="text-highlight">{{ \Carbon\Carbon::parse($order->subscription->pause_end_date)->format('Y-m-d') }}</span>.
                </div>
            @endif
            </div>
       
    </div>
    
    @empty
    <p>No subscription orders found.</p>
@endforelse
    </div>
  </div>
</div>

@endsection

@section('scripts')
<!-- Include SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Open the modal and set the date range
    function openPauseModal(startDate, endDate) {
        const modal = document.getElementById('pauseModal');
        const startDateField = document.getElementById('pause_start_date');
        const endDateField = document.getElementById('pause_end_date');
        const subscriptionStartText = document.getElementById('subscriptionStart');
        const subscriptionEndText = document.getElementById('subscriptionEnd');
        
        // Set the date range text
        subscriptionStartText.textContent = startDate;
        subscriptionEndText.textContent = endDate;
        
        // Set the min and max attributes for the date fields
        startDateField.setAttribute('min', startDate);
        endDateField.setAttribute('min', startDate);
        endDateField.setAttribute('max', endDate);
        
        // Display the modal
        modal.style.display = "block";
    }

    // Close the modal
    function closePauseModal() {
        const modal = document.getElementById('pauseModal');
        modal.style.display = "none";
    }

    // Handle form submission via AJAX
    document.getElementById('pauseForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const startDate = document.getElementById('pause_start_date').value;
        const endDate = document.getElementById('pause_end_date').value;
        const orderId = document.querySelector('input[name="order_id"]').value;

        // Check if the dates are valid
        if (startDate && endDate && startDate <= endDate) {
            const formData = new FormData();
            formData.append('pause_start_date', startDate);
            formData.append('pause_end_date', endDate);
            formData.append('order_id', orderId);
            formData.append('_token', document.querySelector('input[name="_token"]').value); // CSRF token

            // Make AJAX request to pause the subscription
            fetch(`/subscription/${orderId}/pause`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success === 200) {
                    // SweetAlert success message
                    Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    closePauseModal(); // Close the modal
                    location.reload(); // Reload the page
                });
            }  else {
                    // SweetAlert error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error pausing subscription:', error);
                // SweetAlert error message for unexpected issues
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while pausing the subscription.',
                    confirmButtonText: 'OK'
                });
            });
        } else {
            // SweetAlert warning for invalid dates
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Dates!',
                text: 'Please select valid dates.',
                confirmButtonText: 'OK'
            });
        }
    });
</script>


<script>
    // Open the Resume Modal and set the pause dates
    function openResumeModal(pauseStartDate, pauseEndDate) {
        const modal = document.getElementById('resumeModal');
        const resumeDateField = document.getElementById('resume_date');
        
        // Set the min and max for the resume date field
        resumeDateField.setAttribute('min', pauseStartDate);
        resumeDateField.setAttribute('max', pauseEndDate);

        // Set the default date to the pause start date
        resumeDateField.value = pauseStartDate;

        // Display the modal
        modal.style.display = "block";
    }

    // Close the Modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = "none";
    }

    // Handle form submission for resuming the subscription via AJAX
    document.getElementById('resumeForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const resumeDate = document.getElementById('resume_date').value; // Get the resume date
        const orderId = document.getElementById('order_id').value; // Get the order ID

        // Create form data
        const formData = new FormData();
        formData.append('resume_date', resumeDate);
        formData.append('order_id', orderId);
        formData.append('_token', document.querySelector('input[name="_token"]').value); // CSRF token

        // Send the form data to the server using fetch
        fetch(`/subscription/${orderId}/resume`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success === 200) {
                // SweetAlert success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    closeModal('resumeModal'); // Close the modal
                    location.reload(); // Reload the page after closing the modal
                });

            } else {
                // SweetAlert error message
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message,
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error resuming subscription:', error);
            // SweetAlert error message for unexpected issues
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while resuming the subscription.',
                confirmButtonText: 'OK'
            });
        });
    });
</script>




@endsection
