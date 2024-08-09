@extends('user.layouts.front-dashboard')

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
  div#Message {
    background-color: #def2d7;
    /* color: #fff; */
    color: #5b7052;
    padding: 17px;
    font-size: 18px;
    margin: 16px 0px;
    border-radius: 5px;
}
</style>
@endsection

@section('content')

<div class="dashboard__main">
    <div class="dashboard__content bg-light-2">
      <div class="row y-gap-20 justify-between items-end pb-10 mt-30 lg:pb-10 md:pb-32">
        <div class="col-auto">

          <h1 class="text-30 lh-14 fw-600">Profile</h1>
         
        </div>

        <div class="col-auto">

        </div>
      </div>


      <div class="py-30 px-30 rounded-4 bg-white shadow-3">
        <div class="tabs -underline-2 js-tabs">
          <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 js-tabs-controls">

            <div class="col-auto">
              <button class="tabs__button text-18 lg:text-16 text-light-1 fw-500 pb-5 lg:pb-0 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">Personal Information</button>
            </div>

           

          </div>
          <div class="tabs__content pt-10 js-tabs-content">
            @if(session()->has('success'))
            <div class="text-success-2 lh-1 fw-500" id="Message">
                {{ session()->get('success') }}
            </div>
            @endif
        
            @if ($errors->has('danger'))
                <div class="alert alert-danger" id="Message">
                    {{ $errors->first('danger') }}
                </div>
            @endif
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
           @endif
          <form action="{{ route('user.updateProfile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
          
            <div class="tabs__pane -tab-item-1 is-tab-el-active">
              <div class="row y-gap-30 items-center">
                  <div class="col-auto">
                    <div class="d-flex ratio ratio-1:1 w-200">
                        @php
                            $userPhoto = Auth::guard('users')->user()->userphoto;
                            $photoUrl = $userPhoto ? asset('storage/' . $userPhoto) : asset('front-assets/img/images.jfif');
                        @endphp
                
                        <img src="{{ $photoUrl }}" alt="image" class="img-ratio rounded-4">
                
                        <div class="d-flex justify-end px-10 py-10 h-100 w-1/1 absolute">
                            <div class="size-30 bg-white rounded-4 text-center">
                                <a href="#" id="delete-photo-btn"><i class="icon-trash text-16"></i></a>
                            </div>
                        </div>
                    </div>
                  </div>
  
                  <div class="col-auto">
                      <h4 class="text-16 fw-500">Your avatar</h4>
                      <div class="text-14 mt-5">PNG or JPG no bigger than 800px wide and tall.</div>
  
                      <div class="d-inline-block mt-15">
                          <input type="file" name="userphoto" class="form-control">
                      </div>
                  </div>
              </div>
  
              <div class="border-top-light mt-30 mb-30"></div>
  
              <div class="col-xl-12">
                  <div class="row x-gap-20 y-gap-20">
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="full_name">Full name (First and Last name) *</label>
                                  <input type="text" class="form-control" id="full_name" name="name" value="{{ Auth::guard('users')->user()->name }}" placeholder="Enter Name">
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="mobile_number">Mobile number</label>
                                  <input type="text" class="form-control" id="mobile_number" name="phonenumber" value="{{ Auth::guard('users')->user()->mobile_number }}" placeholder="Enter Mobile number" readonly>
                              </div>
                          </div>
                      </div>
  
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label for="email">Email</label>
                                  <input type="text" class="form-control" id="email" name="email" value="{{ Auth::guard('users')->user()->email }}" placeholder="Enter Email">
                              </div>
                          </div>
                          {{-- <div class="col-md-6">
                              <div class="form-group">
                                  <label for="date_of_birth">Date of Birth</label>
                                  <input type="date" class="form-control" id="date_of_birth" name="dob" value="{{ Auth::guard('users')->user()->dob }}">
                              </div>
                          </div> --}}
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="gender">Gender</label><br>
                              <input type="radio" name="gender" id="female" value="Female" {{ Auth::guard('users')->user()->gender == 'Female' ? 'checked' : '' }}>
                              <label for="female">Female</label><br>
                              
                              <input type="radio" name="gender" id="male" value="Male" {{ Auth::guard('users')->user()->gender == 'Male' ? 'checked' : '' }}>
                              <label for="male">Male</label><br>
                              
                              <input type="radio" name="gender" id="other" value="Other" {{ Auth::guard('users')->user()->gender == 'Other' ? 'checked' : '' }}>
                              <label for="other">Other</label>
                          </div>
                      </div>
                      
                       
                    </div>
  
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label for="about_yourself">About Yourself</label>
                                  <textarea name="about" class="form-control" id="about_yourself" rows="10">{{ Auth::guard('users')->user()->about }}</textarea>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
  
              <div class="d-inline-block pt-30">
                  <button type="submit" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                      Save Changes <div class="icon-arrow-top-right ml-15"></div>
                  </button>
              </div>
          </div>
          </form>
            {{-- <div class="tabs__pane -tab-item-2">
              <div class="col-xl-9">
                <div class="row x-gap-20 y-gap-20">
                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">Address Line 1</label>
                    </div>

                  </div>

                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">Address Line 2</label>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">City</label>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">State</label>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">Select Country</label>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">ZIP Code</label>
                    </div>

                  </div>

                  <div class="col-12">
                    <div class="d-inline-block">

                      <a href="#" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                        Save Changes <div class="icon-arrow-top-right ml-15"></div>
                      </a>

                    </div>
                  </div>
                </div>
              </div>
            </div> --}}

            {{-- <div class="tabs__pane -tab-item-3">
              <div class="col-xl-9">
                <div class="row x-gap-20 y-gap-20">
                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">Current Password</label>
                    </div>

                  </div>

                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">New Password</label>
                    </div>

                  </div>

                  <div class="col-12">

                    <div class="form-input ">
                      <input type="text" required>
                      <label class="lh-1 text-16 text-light-1">New Password Again</label>
                    </div>

                  </div>

                  <div class="col-12">
                    <div class="row x-gap-10 y-gap-10">
                      <div class="col-auto">

                        <a href="#" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                          Save Changes <div class="icon-arrow-top-right ml-15"></div>
                        </a>

                      </div>

                      <div class="col-auto">
                        <button class="button h-50 px-24 -blue-1 bg-blue-1-05 text-blue-1">Cancel</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div> --}}
          </div>
          
        </div>
      </div>


     
    </div>
  </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>

  document.addEventListener('DOMContentLoaded', function () {
      document.getElementById('delete-photo-btn').addEventListener('click', function (e) {
          e.preventDefault();
          if (confirm('Are you sure you want to delete your photo?')) {
              deletePhoto();
          }
      });

      function deletePhoto() {
          axios.delete('{{ route('delete.user.photo') }}', {
              headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              }
          })
          .then(function (response) {
              // Handle success, e.g., update UI, reload page, etc.
              location.reload(); // Example: reload the page
          })
          .catch(function (error) {
              // Handle error, e.g., show error message
              console.error('Error deleting photo:', error);
          });
      }
  });
</script>

@endsection