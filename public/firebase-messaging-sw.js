// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js");
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/

firebase.initializeApp({
    apiKey: "AIzaSyCoCOEnQKgL8pgo3xKeSgzjdz1qwtpImS0",
    authDomain: "fcmv1-9dba6.firebaseapp.com",
    projectId: "fcmv1-9dba6",
    storageBucket: "fcmv1-9dba6.appspot.com",
    messagingSenderId: "664024519893",
    appId: "1:664024519893:web:5b34d39be21b6b28ce7e5c"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
    console.log("Message received.", payload);
    const title = "Hello world is awesome";
    const options = {
        body: "Your notificaiton message .",
        icon: "/firebase-logo.png"
    };
    return self.registration.showNotification(title, options);
});

// self.addEventListener('notificationclick', function(event) {
//     console.log('event', event)
//     console.log('action', event.action)
//     console.log('data', event.notification.data.FCM_MSG.notification.data)
    
//     if(event.action == "subscribe"){
//         console.log('pgm')
//     }

//     event.notification.close();
//  }); 
