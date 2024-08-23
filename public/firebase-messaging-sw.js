// Initialize Firebase in your main application
// Ensure the Firebase scripts are included in your HTML before this script runs
// Example:


// Firebase configuration (same as in your service worker)

const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_AUTH_DOMAIN",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_STORAGE_BUCKET",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID",
    measurementId: "YOUR_MEASUREMENT_ID"
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
        const audio = new Audio('D:/xampp/htdocs/33crores/panditWeb/public/Ghanti.mp3');
        audio.play();
    }
});
