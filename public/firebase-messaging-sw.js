importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

// Initialize Firebase
firebase.initializeApp({
    apiKey: "AIzaSyDnr12fJbycTY67cj3q78PEAMG_0D74jTc",
    authDomain: "pandit-cd507.firebaseapp.com",
    projectId: "pandit-cd507",
    storageBucket: "pandit-cd507.appspot.com",
    messagingSenderId: "696430656576",
    appId: "1:696430656576:web:0b5462793e668b0abe33a5",
    measurementId: "G-X7N1W6XCDJ"
});

const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification.title || "Default Title";
    const notificationOptions = {
        body: payload.notification.body || "Default body content.",
        icon: payload.notification.icon || '/path/to/default/icon.png',
        image: payload.data.image || 'https://pandit.33crores.com/uploads/profile_photo/1724323767.jpg', // Pooja image URL
        actions: [
            {
                action: 'open_url',
                title: 'Go to Dashboard'
            }
        ],
        data: {
            url: payload.data.url // URL to open when notification is clicked
        }
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close(); // Close the notification

    if (event.action === 'open_url' && event.notification.data.url) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url) // Open the URL when the notification is clicked
        );
    }
});
