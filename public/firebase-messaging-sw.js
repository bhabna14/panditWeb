// Initialize Firebase in your main application
// Ensure the Firebase scripts are included in your HTML before this script runs
// Example:


// Firebase configuration (same as in your service worker)



const firebaseConfig = {
    apiKey: "AIzaSyDnr12fJbycTY67cj3q78PEAMG_0D74jTc",
    authDomain: "pandit-cd507.firebaseapp.com",
    projectId: "pandit-cd507",
    storageBucket: "pandit-cd507.appspot.com",
    messagingSenderId: "696430656576",
    appId: "1:696430656576:web:0b5462793e668b0abe33a5",
    measurementId: "G-X7N1W6XCDJ"
};


// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Retrieve Firebase Messaging object.
const messaging = firebase.messaging();

// Register Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
    .then(function(registration) {
        console.log('Service Worker registered with scope:', registration.scope);
        messaging.useServiceWorker(registration);

        // Request permission and get token
        requestNotificationPermission();
    })
    .catch(function(err) {
        console.error('Service Worker registration failed:', err);
    });
}

// Function to request permission for notifications
function requestNotificationPermission() {
    messaging.requestPermission()
    .then(function() {
        console.log('Notification permission granted.');
        // Get FCM token
        return messaging.getToken();
    })
    .then(function(token) {
        console.log('FCM Token:', token);
        // TODO: Send the token to your server and save it to send notifications later
    })
    .catch(function(err) {
        console.error('Unable to get permission to notify.', err);
    });
}

// Handle incoming messages while the app is in the foreground
messaging.onMessage(function(payload) {
    console.log('Message received. ', payload);
    // Customize notification here
    const notificationTitle = payload.notification.title || "Default Title";
    const notificationOptions = {
        body: payload.notification.body || "Default body content.",
        icon: payload.notification.icon || "/firebase-logo.png" // Ensure this path is correct
    };

    // Display notification
    if (Notification.permission === 'granted') {
        new Notification(notificationTitle, notificationOptions);

        // Play custom sound
        const audio = new Audio('/var/www/panditWeb/public/Ghanti.mp3');
        audio.play();

        notification.onclick = function(event) {
            event.preventDefault(); // Prevent the browser from focusing the Notification's tab
            window.open(payload.data.url, '_blank'); // Open the URL in a new tab
        };
    }
});



messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon || '/firebase-logo.png',
        data: {
            url: payload.data.url
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click event
self.addEventListener('notificationclick', function(event) {
    event.notification.close(); // Close the notification

    if (event.notification.data.url) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url) // Open the URL when the notification is clicked
        );
    }
});

