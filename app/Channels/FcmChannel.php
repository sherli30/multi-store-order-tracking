<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Client;

class FcmChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $fcmData = $notification->toFcm($notifiable);
        if (!$fcmData) return;

        // Get all tokens for the user
        $tokens = $notifiable->fcmTokens()->pluck('token')->toArray();
        if (empty($tokens)) return;

        $credentialsPath = storage_path('firebase_credentials.json');
        if (!file_exists($credentialsPath)) {
            Log::warning('[FCM Channel] Credentials missing. Cannot send push notification.', [
                'user_id' => $notifiable->id,
                'expected_path' => $credentialsPath
            ]);
            return;
        }

        try {
            Log::info('[FCM Channel] Credentials file detected.', ['path' => $credentialsPath]);
            $client = new Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            $token = $client->fetchAccessTokenWithAssertion();
            if (!isset($token['access_token'])) {
                Log::error('[FCM Channel] Failed to generate OAuth token from Google API.');
                throw new \Exception("Failed to fetch FCM access token");
            }
            $accessToken = $token['access_token'];
            Log::info('[FCM Channel] OAuth token generated successfully.');

            $projectId = json_decode(file_get_contents($credentialsPath), true)['project_id'] ?? null;
            if (!$projectId) {
                Log::error('[FCM Channel] project_id missing from credentials file.');
                return;
            }
            Log::info('[FCM Channel] Project ID detected.', ['project_id' => $projectId]);

            $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            foreach ($tokens as $deviceToken) {
                $payload = [
                    'message' => [
                        'token' => $deviceToken,
                        'notification' => [
                            'title' => $fcmData['title'] ?? 'Notification',
                            'body' => $fcmData['body'] ?? '',
                        ],
                        'data' => $fcmData['data'] ?? [],
                    ]
                ];

                Log::info('[FCM Channel] Sending FCM request.', ['endpoint' => $endpoint, 'token_prefix' => substr($deviceToken, 0, 10) . '...']);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ])->post($endpoint, $payload);

                if ($response->successful()) {
                    Log::info('[FCM Channel] FCM response received: SUCCESS.', ['response' => $response->json()]);
                } else {
                    Log::error('[FCM Channel] FCM Send Failed', [
                        'status' => $response->status(), 
                        'response' => $response->body(), 
                        'token' => $deviceToken
                    ]);
                    // If token is invalid/unregistered, we should delete it
                    if ($response->status() === 404 || $response->status() === 400 || str_contains($response->body(), 'UNREGISTERED') || str_contains($response->body(), 'INVALID_ARGUMENT')) {
                        \App\Models\FcmToken::where('token', $deviceToken)->delete();
                        Log::info('[FCM Channel] Deleted unregistered token.', ['token' => $deviceToken]);
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('[FCM Channel] Error: ' . $e->getMessage());
        }
    }
}
