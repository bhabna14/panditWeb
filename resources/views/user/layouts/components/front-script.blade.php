<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAz77U5XQuEME6TpftaMdX0bBelQxXRlM"></script>

<script src="../../../unpkg.com/%40googlemaps/markerclusterer%402.0.10/dist/index.min.js"></script>
<script src="{{asset('front-assets/js/vendors.js')}}"></script>
<script src="{{asset('front-assets/js/main.js')}}"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script> --}}
{{-- <script src="https://code.jquery.com/jquery-1.12.0.min.js"></script> --}}
<script>
    AOS.init();
  </script>
  <script>
    let chunks = [];
    let mediaRecorder;
  
    document.getElementById('startRecord').addEventListener('click', function() {
      navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
          mediaRecorder = new MediaRecorder(stream);
          mediaRecorder.ondataavailable = function(e) {
            chunks.push(e.data);
          };
          mediaRecorder.onstop = function() {
            let blob = new Blob(chunks, { type: 'audio/ogg; codecs=opus' });
            chunks = [];
            let audioURL = URL.createObjectURL(blob);
            document.getElementById('recordedAudio').src = audioURL;
          };
          mediaRecorder.start();
          document.getElementById('startRecord').disabled = true;
          document.getElementById('stopRecord').disabled = false;
        })
        .catch(err => console.error('getUserMedia Error: ', err));
    });
  
    document.getElementById('stopRecord').addEventListener('click', function() {
      mediaRecorder.stop();
      document.getElementById('startRecord').disabled = false;
      document.getElementById('stopRecord').disabled = true;
    });
  </script>
  <script>
  
    let copybtn = document.querySelector(".copybtn");
    
    
    function copyIt(){
      let copyInput = document.querySelector('#copyvalue');
    
      copyInput.select();
    
      document.execCommand("copy");
    
      copybtn.textContent = "COPIED";
    }
    
    </script>

    <script>
      $(document).ready(function() {
    $("#news-slider").owlCarousel({
        items : 3,
        itemsDesktop:[1199,3],
        itemsDesktopSmall:[980,2],
        itemsMobile : [600,1],
        navigation:true,
        navigationText:["",""],
        pagination:true,
        autoPlay:true
    });
});
    </script>