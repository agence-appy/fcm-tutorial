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
    protected $signature = 'fcm';

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
