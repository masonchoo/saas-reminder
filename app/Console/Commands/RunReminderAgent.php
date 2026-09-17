<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Carbon\Carbon;
use illuminate\Support\Facades\Http;
use App\Models\Subscription;

#[Signature('app:run-reminder-agent')]
#[Description('Command description')]
class RunReminderAgent extends Command
{
protected $signature = 'agent:send-reminders';
    protected $description = 'AI Agent that drafts and sends WhatsApp reminders';

    public function handle()
    {
        // 1. Find subscriptions due in exactly 3 days
        $targetDate = Carbon::now()->addDays(3)->toDateString();
        
        $dueSubscriptions = Subscription::whereDate('next_due_date', $targetDate)->get();

        if ($dueSubscriptions->isEmpty()) {
            $this->info('No reminders needed today.');
            return;
        }

        foreach ($dueSubscriptions as $sub) {
            // 2. Ask OMLX to generate a personalized message
            $message = $this->generateAIMessage($sub);

            // 3. Send to your WhatsApp Node.js microservice
            $this->sendToWhatsApp($sub->phone_number, $message); // Assuming you add a phone_number column

            $this->info("Sent reminder to {$sub->account_name}");
        }
    }

    private function generateAIMessage($subscription)
    {
        // OMLX usually exposes an OpenAI-compatible API on localhost
        $response = Http::post(env('OMLX_API_URL'), [
            'model' => 'llama-3', // Replace with your downloaded OMLX model name
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => 'You are a polite, friendly AI assistant. Write a short WhatsApp message reminding the user their subscription is due. Keep it under 3 sentences.'
                ],
                [
                    'role' => 'user', 
                    'content' => "Name: {$subscription->account_name}. Subscription: {$subscription->subscription_title}. Due Date: {$subscription->next_due_date->format('M d, Y')}."
                ]
            ],
            'temperature' => 0.7
        ]);

        return $response->json('choices.0.message.content');
    }

    private function sendToWhatsApp($phone, $message)
    {
        // This calls a local Node.js server running whatsapp-web.js
        Http::post('http://127.0.0.1:3000/send-message', [
            'number' => $phone,
            'message' => $message
        ]);
    }
}
