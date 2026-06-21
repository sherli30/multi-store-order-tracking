<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\GeneralOrderNotification;
use Illuminate\Support\Facades\Notification;

class TestFcmNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:test {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test FCM notification to a specific user to verify the delivery pipeline.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $tokens = $user->fcmTokens()->pluck('token')->toArray();
        if (empty($tokens)) {
            $this->error("User {$user->name} (ID: {$userId}) has no registered FCM tokens in the fcm_tokens table.");
            return 1;
        }

        $this->info("Found " . count($tokens) . " token(s) for user {$user->name}.");
        $this->info("Dispatching test notification...");

        $user->notify(new GeneralOrderNotification([
            'order_id' => 0,
            'title'    => 'Test Notification',
            'message'  => 'This is a test notification from the Laravel backend to verify FCM delivery.',
            'type'     => 'test',
            'status'   => 'test_status',
        ]));

        $this->info("Notification dispatched to the queue/channel.");
        $this->info("Please check the laravel.log file for FCM Channel diagnostic outputs.");

        return 0;
    }
}
