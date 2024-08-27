importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

// Initialize Firebase
const firebaseConfig = {
    apiKey: "AIzaSyB3aKiSjmfmnFaL_FkY_Wt0C14hgURwPeQ",
    authDomain: "pandit-user.firebaseapp.com",
    projectId: "pandit-user",
    storageBucket: "pandit-user.appspot.com",
    messagingSenderId: "251995088901",
    appId: "1:251995088901:web:c3b4b638ef6c3c18e4146c",
    measurementId: "G-E2819B9WMS"
  };

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
