<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Subscription;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        // ---------------------------------------------------------
        // 1. HANDLE BUTTON CLICKS (Callback Queries)
        // ---------------------------------------------------------
        if (isset($payload['callback_query'])) {
            $callbackData = $payload['callback_query']['data'];
            $chatId = $payload['callback_query']['message']['chat']['id'];
            $messageId = $payload['callback_query']['message']['message_id'];

            if ($callbackData === 'confirm_reminder') {
                // Here you would find their subscription by $chatId and activate the reminder
                $this->editMessage($chatId, $messageId, "✅ Great! I've activated the reminder.");
            } elseif ($callbackData === 'cancel_reminder') {
                $this->editMessage($chatId, $messageId, "❌ Okay, I've cancelled that request.");
            }
            
            return response()->json(['status' => 'ok']);
        }

        // ---------------------------------------------------------
        // 2. HANDLE TEXT MESSAGES
        // ---------------------------------------------------------
        if (isset($payload['message']['text'])) {
            $chatId = $payload['message']['chat']['id'];
            $text = $payload['message']['text'];

            // Deep link handling (Your existing /start logic)
            if (str_starts_with($text, '/start ')) {
                $subscriptionId = str_replace('/start sub_', '', $text);
                $subscription = Subscription::find($subscriptionId);
                
                if ($subscription) {
                    $subscription->update(['telegram_chat_id' => $chatId]);
                    
                    // Ask them right away if they want reminders enabled!
                    $this->sendConfirmationKeyboard(
                        $chatId, 
                        "✅ Success! Connected to {$subscription->subscription_title}.\n\nDo you want me to send you an automatic reminder 3 days before it is due?"
                    );
                } else {
                    $this->sendMessage($chatId, "❌ Invalid subscription link.");
                }
                return response()->json(['status' => 'ok']);
            }

            // Normal typing (Send to AI)
            // Example: User types "Remind me next Friday"
            $aiReply = $this->askOmlxAi($text);
            $this->sendMessage($chatId, $aiReply);
        }

        return response()->json(['status' => 'ok']);
    }

    private function askOmlxAi($text)
    {
        try {
            $response = Http::post('http://127.0.0.1:8080/v1/chat/completions', [
                'model' => 'llama-3',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful subscription reminder bot.'],
                    ['role' => 'user', 'content' => $text]
                ]
            ]);
            return $response->json('choices.0.message.content') ?? 'I could not process that request.';
        } catch (\Exception $e) {
            return 'AI system is currently offline.';
        }
    }

    

    private function sendConfirmationKeyboard($chatId, $text)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Yes, send reminder', 'callback_data' => 'confirm_reminder'],
                    ['text' => '❌ No thanks', 'callback_data' => 'cancel_reminder']
                ]
            ]
        ];

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    private function editMessage($chatId, $messageId, $newText)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        
        Http::post("https://api.telegram.org/bot{$token}/editMessageText", [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $newText,
            // Passing an empty reply_markup removes the buttons
            'reply_markup' => json_encode(['inline_keyboard' => []])
        ]);
    }
private function sendMessage($chatId, $text)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }
    
}
