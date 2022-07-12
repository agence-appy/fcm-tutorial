<?php

namespace App\Http\Controllers;

use App\Models\User;
use Appy\FcmHttpV1\FcmNotification;
use Appy\FcmHttpV1\FcmTopicHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FCMController extends Controller
{
    public function registerToken(Request $req){
        $user = new User();
        $user->device_token = $req->token;
        $user->save();
    }
}
