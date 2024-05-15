@extends('user.layouts.front-dashboard')

@section('styles')
@endsection

@section('content')

<div class="dashboard__main">
  <div class="dashboard__content bg-light-2">
    <div class="row y-gap-20 justify-center items-center pb-30 mt-30 lg:pb-40 md:pb-32 text-center">
        <div class="rating-container mt-20">
            <!-- Rating input with 5 stars -->
            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5">
                <label for="star5"></label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4"></label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3"></label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2"></label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1"></label>
            </div>
        </div>
        <div class="col-md-7 text-center form-input">
            <label for="">Message</label>
            {{-- <textarea name="" id="" cols="30" rows="10" class="form-control"></textarea> --}}
            <textarea name="" id="" class="form-control" cols="30" spellcheck="false"></textarea>
        </div>
        <h6>Upload Audio</h6>
        <div class="col-md-7 text-center form-input">
           
            <input type="file" class="form-control" name="audioFile" accept="audio/*">
        </div>
        <div class="col-md-7">
            
            <audio id="recordedAudio" controls></audio>
        </div>
        <div class="col-md-7">
            <button id="startRecord" >Start Recording</button>
            <button id="stopRecord" disabled>Stop Recording</button>
        </div>
        <h6>Upload Image</h6>
        <div class="col-md-7 text-center form-input">
           
            <input type="file" class="form-control" name="image" accept="audio/*">
        </div>
       <div class="col-md-12">
        <button type="submit" class=" rating-submit">Submit</button>
       </div>
    </div>

</div>
</div>



@endsection

@section('scripts')
@endsection