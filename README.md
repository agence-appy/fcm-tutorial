# FCM V1 LARAVEL TUTORIAL

This tutorial will teach you step by step how to implement FCM V1 in your Laravel project.

# Requirements

## Firebase configuration

To configure Firebase Cloud Messaging V1, please refer to the [Firebase Install](https://github.com/agence-appy/fcmhttpv1) section. 

## Laravel configuration

To configure Laravel project, please refer to the [Laravel Install](https://github.com/agence-appy/fcmhttpv1) section.

## PWA Configuration

Please follow this [tutorial](https://github.com/silviolleite/laravel-pwa).

# FCM V1 Implementation

## Migration

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
File created under app/Console/Commands/<your command>

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
