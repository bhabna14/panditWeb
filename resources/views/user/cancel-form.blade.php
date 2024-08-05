@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')
<div class="dashboard__main">
    <div class="dashboard__content bg-light-2">
        <div class="row y-gap-20 justify-between items-end pb-30 mt-30 lg:pb-40 md:pb-32">
            <div class="col-auto">
    
            <h1 class="text-30 lh-14 fw-600">Cancel Pooja</h1>
            {{-- <div class="text-15 text-light-1">Lorem ipsum dolor sit amet, consectetur.</div> --}}
    
            </div>
    
            <div class="col-auto">
    
            </div>
        </div>
        <div class="row">
           
            <form action="{{ route('cancelBooking', ['booking_id' => $booking->id]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="cancel_reason">Reason for Cancellation</label>
                    <textarea name="cancel_reason" id="cancel_reason" rows="5" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label for="refund_method">Refund Method</label>
                    <select name="refund_method" id="refund_method" class="form-control" required>
                        <option value="original">Back to Original Payment Method</option>
                    </select>
                </div>
                <button type="submit" class="button px-10 fw-400 text-14 -blue-1 bg-dark-4 h-50 text-white" style="margin-top:20px">Confirm Cancellation</button>
            </form>
            
            
        </div>
    </div>
</div>


@endsection



@section('scripts')
<script>
    document.getElementById('refund_method').addEventListener('change', function() {
        var bankDetailsSection = document.getElementById('bank_details_section');
        if (this.value === 'bank') {
            bankDetailsSection.style.display = 'block';
        } else {
            bankDetailsSection.style.display = 'none';
        }
    });
</script>
@endsection