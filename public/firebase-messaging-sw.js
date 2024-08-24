// Import and initialize Firebase in the service worker
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

// Firebase configuration (same as in your main app)
firebase.initializeApp({
    apiKey: "AIzaSyDnr12fJbycTY67cj3q78PEAMG_0D74jTc",
    authDomain: "pandit-cd507.firebaseapp.com",
    projectId: "pandit-cd507",
    storageBucket: "pandit-cd507.appspot.com",
    messagingSenderId: "696430656576",
    appId: "1:696430656576:web:0b5462793e668b0abe33a5",
    measurementId: "G-X7N1W6XCDJ"
});

// Retrieve an instance of Firebase Messaging so that it can handle background messages
const messaging = firebase.messaging();

// Handle background messages
messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification.title || "Default Title";
    const notificationOptions = {
        body: payload.notification.body || "Default body content.",
        icon: payload.notification.icon || '/firebase-logo.png',
        data: {
            url: payload.data.url
        }
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
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
