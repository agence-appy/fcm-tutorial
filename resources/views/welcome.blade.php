<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>
    @laravelPWA
</head>

<body>
    <script src='https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js'></script>
    <script src='https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js'></script>
    <script type="text/javascript">
        const firebaseConfig = {
            apiKey: "{{ config('fcm_config.firebase_config.apiKey') }}",
            authDomain: "{{ config('fcm_config.firebase_config.authDomain') }}",
            projectId: "{{ config('fcm_config.firebase_config.projectId') }}",
            storageBucket: "{{ config('fcm_config.firebase_config.storageBucket') }}",
            messagingSenderId: "{{ config('fcm_config.firebase_config.messagingSenderId') }}",
            appId: "{{ config('fcm_config.firebase_config.appId') }}",
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        console.log(csrfToken)
        messaging
            .requestPermission()
            .then(() =>
                messaging.getToken()
            )
            .then(function(res) {
                // fetch('{{ route('register-token') }}', {
                //         method: 'post',
                //         headers: {
                //             'Content-Type': 'application/json',
                //             "X-CSRF-TOKEN": csrfToken
                //         }
                //         body: JSON.stringify({
                //             _token: csrfToken,
                //             token: res
                //         })
                //     },
                // })
                fetch("{{ route('register-token') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _token: csrfToken,
                            token: res
                        })
                    })
                    .catch(error => console.error(error));
            })
            .catch(function(err) {
                console.error('catch', err);
            });
    </script>
</body>

</html>
