@extends('pandit.layouts.custom-app')

@section('styles')
<title>Pandit Login</title>
@endsection

@section('class')
    <div class="bg-primary">
@endsection

@section('content')
<div class="page-single">
    <div class="container">
        <div class="row">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-8 col-xs-10 card-sigin-main mx-auto my-auto py-45 justify-content-center">
                <div class="card-sigin mt-5 mt-md-0">
                    <div class="main-card-signin d-md-flex">
                        <div class="wd-100p">
                            <div class="d-flex mb-4">
                                <a href="#"><img src="{{ asset('assets/img/brand/logo.png') }}" class="sign-favicon ht-40" alt="logo"></a>
                            </div>
                            <div class="">
                                <div class="main-signup-header">
                                    <div class="panel panel-primary">
                                        <div class="tab-menu-heading mb-2 border-bottom-0">
                                            <div class="tabs-menu1">
                                                <ul class="nav panel-tabs" style="display: flex; justify-content: space-between">
                                                    <li class="me-2"><a href="#tab5" class="active" data-bs-toggle="tab">Login</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="panel-body tabs-menu-body border-0 p-3">
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab5">
                                                    @if (session('message'))
                                                        <div class="alert alert-success">
                                                            {{ session('message') }}
                                                        </div>
                                                    @elseif (session('error'))
                                                        <div class="alert alert-danger">
                                                            {{ session('error') }}
                                                        </div>
                                                    @endif
                                        
                                                    @if (session('otp_sent'))
                                                        <form action="/verify-otp" method="POST">
                                                            @csrf
                                                            <input type="hidden" id="device_id" name="device_id" value="" required>
                                                            <input type="hidden" id="platform" name="platform" value="" required> <!-- Hidden field to store Platform -->
                                                        
                                                            <input type="hidden" class="form-control" name="order_id" value="{{ session('otp_order_id') }}" required>
                                                            <input type="text" class="form-control" name="otp" placeholder="Enter OTP" required>
                                                            <input type="hidden" class="form-control" name="phone" value="{{ session('otp_phone') }}" required>
                                                            
                                                            <button type="submit" class="btn btn-primary" style="margin-top: 20px">Verify OTP</button>
                                                        </form>
                                                    @else
                                                        <form action="/send-otp" method="POST">
                                                            @csrf
                                                            <div id="step1">
                                                                <div class="form-group">
                                                                    <div style="display: flex; align-items: center;">
                                                                        <input type="text" class="form-control" value="+91" readonly style="background-color: #f1f1f1; width: 60px; text-align: center;">
                                                                        <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" style="margin-left: 5px; flex: 1;">
                                                                    </div>
                                                                </div>
                                                                {{-- <input type="hidden" name="onesignal_player_id" id="onesignal_player_id"> --}}
                                                                <input type="submit" class="btn btn-primary" value="Generate OTP">
                                                            </div>
                                                        </form>
                                                    @endif
                                        
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    setTimeout(function() {
        var message = document.querySelector('.alert');
        if (message) {
            message.style.display = 'none';
        }
    }, 3000);
</script>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
  window.OneSignal = window.OneSignal || [];
  OneSignal.push(function() {
    // Initialize OneSignal
    OneSignal.init({
      appId: "c1804718-4422-4f50-bac9-b23b48de52f4", // Replace with your OneSignal App ID
      notifyButton: {
        enable: true, // Enable the notification bell
      },
      serviceWorkerPath: 'OneSignalSDKWorker.js', // Optional, if custom path
      autoResubscribe: true, // Automatically resubscribe the user when they revisit the site
      persistNotification: true // Keep the notification visible until the user interacts with it
    });

    // Request permission for notifications (if not already granted)
    OneSignal.isPushNotificationsEnabled(function(isEnabled) {
      if (isEnabled) {
        console.log('Push notifications are enabled!');
        getPlayerId();
      } else {
        console.log('Push notifications are not enabled.');
        // Show the notification prompt and then get the playerId
        OneSignal.showSlidedownPrompt().then(function() {
          getPlayerId();
        }).catch(function() {
          console.log('Permission not granted or prompt not shown.');
        });
      }
    });

    // Function to retrieve the OneSignal user ID (device ID)
    function getPlayerId() {
      OneSignal.getUserId(function(playerId) {
        if (playerId) {
          document.getElementById('device_id').value = playerId;
          console.log('playerId', playerId);
          alert('Device ID: ' + playerId); // Alerting the playerId
        } else {
          console.log('Failed to retrieve playerId.');
          alert('Failed to retrieve Device ID.');
        }
      });
    }

    // Detect the platform based on user-agent
    var platform = 'web'; // Default to 'web'
    if (navigator.userAgent) {
      var userAgent = navigator.userAgent.toLowerCase();
      if (userAgent.indexOf('android') > -1) {
        platform = 'android';
      } else if (userAgent.indexOf('iphone') > -1 || userAgent.indexOf('ipad') > -1) {
        platform = 'ios';
      }
    }
    document.getElementById('platform').value = platform;
  });
</script>

@endsection