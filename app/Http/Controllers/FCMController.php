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
