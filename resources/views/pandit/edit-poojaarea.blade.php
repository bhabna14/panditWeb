@extends('pandit.layouts.app')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <span class="main-content-title mg-b-0 mg-b-lg-1">UPDATE POOJA AREA</span>
        </div>
        <div class="justify-content-center mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tx-15"><a href="javascript:void(0);">Pages</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </div>
    </div>
    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="custom-card main-content-body-profile">
                <div class="tab-content">
                    <div class="main-content-body tab-pane border-top-0 active" id="poojaarea">
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
                        <form action="{{ route('update.poojaarea', $poojaArea->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                        
                            <div class="form-group">
                                <label for="state">State:</label>
                                <select name="state" id="state" class="form-control" disabled>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->stateCode }}" {{ $poojaArea->state_code == $state->stateCode ? 'selected' : '' }}>{{ $state->stateName }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="state" value="{{ $poojaArea->state_code }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="district">District:</label>
                                <select name="district" id="district" class="form-control" disabled>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->districtCode }}" {{ $poojaArea->district_code == $district->districtCode ? 'selected' : '' }}>{{ $district->districtName }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="district" value="{{ $poojaArea->district_code }}">
                            </div>
                            
                        
                            <div class="form-group">
                                <label for="city">Subdistrict:</label>
                                <select name="city" id="city" class="form-control"  onchange="getVillage(this.value)">>
                                    @foreach ($subdistricts as $subdistrict)
                                        <option value="{{ $subdistrict->subdistrictCode }}" {{ $poojaArea->subdistrict_code == $subdistrict->subdistrictCode ? 'selected' : '' }}>{{ $subdistrict->subdistrictName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <div class="form-group">
                                <label for="village">Village Name</label>
                                <select class="form-control select2" id="village" name="village[]" multiple="multiple">
                                    @foreach ($villages as $village)
                                        <option value="{{ $village->villageCode }}" 
                                            {{ in_array($village->villageCode, explode(',', $poojaArea->village_code)) ? 'selected' : '' }}>
                                            {{ $village->villageName }} 
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/pandit-area.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#village').select2();
        });

        setTimeout(function() {
            document.getElementById('Message').style.display = 'none';
        }, 3000);
    </script>
    <script src="{{ asset('assets/plugins/SmartPhoto-master/smartphoto.js') }}"></script>
    <script src="{{ asset('assets/js/gallery.js') }}"></script>
@endsection
