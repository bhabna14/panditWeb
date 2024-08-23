@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
    <div class="dashboard__content bg-light-2">
        <div class="row y-gap-20 pb-30 mt-30 lg:pb-40 md:pb-32">
            @foreach ($errors->all() as $error)
            <li class="text-red">{{ $error }}</li>
            @endforeach
         

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <img src="{{ asset('assets/img/'.$booking->pooja->poojalist->pooja_photo) }}" alt="{{ $booking->pooja->pooja_name }}">
                    </div>
                    <div class="col-md-9">
                        <h6>{{ $booking->pooja->pooja_name }}</h6>
                        <p>{{ $booking->pandit->title }} {{ $booking->pandit->name }}</p>
                    </div>
                </div>
              
            </div>
            <form action="{{ route('submitOrUpdateRating') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($rating))
                    <input type="hidden" name="rating_id" value="{{ $rating->id }}">
                @endif
                <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                
                <!-- Rating Selection -->
                <div class="rating-container mt-20">
                    <div class="rating">
                        <input type="radio" id="star5" name="rating" value="5" {{ isset($rating) && $rating->rating == 5 ? 'checked' : '' }}>
                        <label for="star5"></label>
                        <input type="radio" id="star4" name="rating" value="4" {{ isset($rating) && $rating->rating == 4 ? 'checked' : '' }}>
                        <label for="star4"></label>
                        <input type="radio" id="star3" name="rating" value="3" {{ isset($rating) && $rating->rating == 3 ? 'checked' : '' }}>
                        <label for="star3"></label>
                        <input type="radio" id="star2" name="rating" value="2" {{ isset($rating) && $rating->rating == 2 ? 'checked' : '' }}>
                        <label for="star2"></label>
                        <input type="radio" id="star1" name="rating" value="1" {{ isset($rating) && $rating->rating == 1 ? 'checked' : '' }}>
                        <label for="star1"></label>
                    </div>
                </div>
            
                <!-- Feedback Message -->
                <div class="col-md-12 form-input">
                    <textarea placeholder="Feedback Message" name="feedback_message" id="feedback_message" class="form-control" cols="30" rows="5" spellcheck="false">{{ isset($rating) ? $rating->feedback_message : '' }}</textarea>
                </div>
            
                <!-- Upload or Record Audio -->
                <div class="row">
                    <div class="col-md-6 form-input">
                        <h6>UploadAudio</h6>
                        <input type="file" class="form-control" name="audioFile" id="audioFile" accept="audio/*">
                        @if(isset($rating) && $rating->audio_file)
                            <audio controls>
                                <source src="{{ asset('storage/' . $rating->audio_file) }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        @endif
                    </div>
                </div>
            
                <!-- Upload Image -->
                <div class="col-md-12">
                    <h6>Upload Image</h6>
                    <input type="file" class="form-control" name="image" id="image" accept="image/*">
                    @if(isset($rating) && $rating->image_path)
                        <img src="{{ asset('storage/' . $rating->image_path) }}" alt="Uploaded Image" class="img-thumbnail" style="max-width: 200px; margin-top: 10px;">
                    @endif
                </div>
            
                <!-- Submit Button -->
                <div class="col-md-12">
                    <button type="submit" id="submitRating" class="rating-submit btn btn-primary" style="margin-top: 30px;">
                        {{ isset($rating) ? 'Update Rating' : 'Submit Rating' }}
                    </button>
                </div>
            </form>
            
            
            
            
            
        </div>
    </div>
</div>





@endsection

@section('scripts')

<script>
    let mediaRecorder;
    let recordedBlobs = [];

    document.getElementById('startRecord').addEventListener('click', async () => {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.ondataavailable = (event) => {
            if (event.data && event.data.size > 0) {
                recordedBlobs.push(event.data);
            }
        };
        mediaRecorder.start();
        document.getElementById('startRecord').disabled = true;
        document.getElementById('stopRecord').disabled = false;
    });

    document.getElementById('stopRecord').addEventListener('click', () => {
        mediaRecorder.stop();
        document.getElementById('startRecord').disabled = false;
        document.getElementById('stopRecord').disabled = true;

        const recordedBlob = new Blob(recordedBlobs, { type: 'audio/webm' });
        const audioURL = window.URL.createObjectURL(recordedBlob);
        document.getElementById('recordedAudio').src = audioURL;

        const audioFileInput = document.getElementById('audioFile');
        const file = new File([recordedBlob], 'recorded_audio.webm', { type: 'audio/webm' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        audioFileInput.files = dataTransfer.files;
    });
</script>


@endsection