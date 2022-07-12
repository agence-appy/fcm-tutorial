# FCM V1 LARAVEL TUTORIAL

Hello world 😎,

If you work in web development, one day you will need to send push notifications. Push Notifications are usefull in so many cases. With them, you can target the right customers or users, increase user retention, boost conversion rates and increase app engagement.

I essentially work with Laravel. In most of projects, clients need to send push notifications to users. To stay up to date, I wanted to use the new [FCM Http V1 API](https://firebase.google.com/docs/cloud-messaging/migrate-v1). The first time I tried to implement it in Laravel, it wasn't really fun. I had to search in so many websites to achieve what I needed. I haven't found any Laravel package with easy install and usage, so I decided to create one.

In this package, you just need to setup your Firebase project and your Laravel application in one go.

**After that, you w'll be able to send notifications in only 2 lines.**

This tutorial will teach you step by step how to implement FCM V1 in your Laravel project. We w'll see how to configure Firebase project and Laravel App. You will see how to generate FCM device tokens, how to subscribe/unsubscribe tokens to topic and finally how two send push notification in the easiest way. Ready ?

# Requirements

## Firebase configuration

To configure Firebase Cloud Messaging V1, please refer to the [Firebase Install](https://github.com/agence-appy/fcmhttpv1) section. 

## Laravel configuration

To configure Laravel project, please refer to the [Laravel Install](https://github.com/agence-appy/fcmhttpv1) section.

# FCM V1 Implementation

## Migration

Create and save a user with the device token.

```php
//database/migrations/2014_10_12_000000_create_users_table.php

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->text('device_token'); // We'll use this field to store the device token
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};

```

## Model

Add device_token to fillable properties.

```php
//app/Models/User.php

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'device_token'
    ];
}
```

## Routes

Create route to store the device token.

```php
//routes/web.php

<?php

use App\Http\Controllers\FCMController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/register-token', [ FCMController::class, 'registerToken'])->name('register-token');
```

## Controller

Create and save an user with the device token.

```php
//app/Http/Controllers/FCMController.php

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FCMController extends Controller
{
    public function registerToken(Request $req){
        $user = new User();
        $user->device_token = $req->token;
        $user->save();
    }
}
```


## View

In this view, we'll generate a FCM device token. Then we'll send it to our controller.
```blade
{{-- /resources/views/welcome.blade.php --}}

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
        
        messaging
            .requestPermission()
            .then(() =>
                messaging.getToken()
            )
            .then(function(res) {
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
```

### Notification

To send notification, we'll use Laravel Command.(We use Laravel Command just for test)

```
php artisan make:command FCM
```
```php
<?php

namespace App\Console\Commands;

use App\Models\User;
use Appy\FcmHttpV1\FcmNotification;
use Appy\FcmHttpV1\FcmTopicHelper;
use Illuminate\Console\Command;

class FCM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '<command name>';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $token = User::find(1)->device_token;

        FcmTopicHelper::subscribeToTopic([$token], "general");
        $notif = new FcmNotification;
        $notif->setTitle("Hello")->setBody("Content here")->setIcon("images/icons/icon-72x72.png")->setTopic("general")->send();
    }
}
```

To send notification, just run :

```
php artisan <command name>
```
