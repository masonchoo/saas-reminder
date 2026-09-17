<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Carbon\Carbon;

class WhatsAppApprovalController extends Controller
{
/**
     * Get pending renewals formatted for WhatsApp interactive messages.
     */
    // public function getPendingApprovals()
    // {
    //     $upcoming = Subscription::where('next_due_date', '<=', Carbon::now()->addDays(7))
    //         ->where('status', '!=', 'paid')
    //         ->get();

    //     if ($upcoming->isEmpty()) {
    //         return response("🎉 *All clear!* No pending subscription approvals.");
    //     }

    //     // Put your bot's full WhatsApp phone number (with country code, no + or spaces)
    //     $botPhone = "60123456789"; 

    //     $message = "🔔 *Pending Subscription Approvals*\n\n";

    //     foreach ($upcoming as $sub) {
    //         // Encode the command into a wa.me URL
    //         $approveLink = "https://wa.me/{$botPhone}?text=" . urlencode("/approve {$sub->id}");
    //         $snoozeLink  = "https://wa.me/{$botPhone}?text=" . urlencode("/snooze {$sub->id}");
    //         $cancelLink  = "https://wa.me/{$botPhone}?text=" . urlencode("/cancel {$sub->id}");

    //         $message .= "📌 *{$sub->subscription_title}*\n";
    //         $message .= "• Account: {$sub->account_name}\n";
    //         $message .= "• Amount: \${$sub->amount}\n";
    //         $message .= "• Due Date: " . Carbon::parse($sub->next_due_date)->format('M d, Y') . "\n";
    //         $message .= "👉 [Approve]({$approveLink}) | [Snooze 3d]({$snoozeLink}) | [Cancel]({$cancelLink})\n\n";
    //     }

    //     return response($message, 200)->header('Content-Type', 'text/plain');
    // }


    public function getPendingApprovals()
{
    $upcoming = Subscription::where('next_due_date', '<=', Carbon::now()->addDays(7))
        ->where('status', '!=', 'paid')
        ->get();

    $botPhone = "0127413218"; 
    $message = "🔔 *Pending Renewal Checks*\n\n";

    foreach ($upcoming as $sub) {
        // Short internal link
        $remindLink = config('app.url') . "/r/{$sub->id}";

        $message .= "📌 *{$sub->subscription_title}* (\${$sub->amount})\n";
        $message .= "• Holder: {$sub->account_name}\n";
        $message .= "• Due: " . Carbon::parse($sub->next_due_date)->format('M d') . "\n";
        $message .= "📩 Remind: {$remindLink}\n\n";
    }

    return response($message, 200)->header('Content-Type', 'text/plain');
}

    /**
     * Handle button callbacks from Hermes when an admin taps a button in WhatsApp.
     */
    public function handleAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|string', // e.g. "approve_12", "snooze_12", "cancel_12"
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $subscription = Subscription::findOrFail($validated['subscription_id']);
        $actionType = strtok($validated['action'], '_');

        switch ($actionType) {
            case 'approve':
                // Advance due date by 1 month and update status
                $subscription->update([
                    'next_due_date' => Carbon::parse($subscription->next_due_date)->addMonth(),
                    'status' => 'paid',
                ]);
                $reply = "✅ Approved! {$subscription->subscription_title} has been marked as paid. Next due date: {$subscription->next_due_date->format('M d, Y')}.";
                break;

            case 'snooze':
                // Snooze reminder for 3 days
                $subscription->update([
                    'next_due_date' => Carbon::parse($subscription->next_due_date)->addDays(3),
                    'status' => 'snoozed',
                ]);
                $reply = "⏰ Snoozed! Reminder for {$subscription->subscription_title} deferred to {$subscription->next_due_date->format('M d, Y')}.";
                break;

            case 'cancel':
                $subscription->update(['status' => 'cancelled']);
                $reply = "❌ Marked for cancellation: {$subscription->subscription_title}. OMLX can draft a vendor cancellation letter if requested.";
                break;

            default:
                return response()->json(['status' => 'error', 'message' => 'Invalid action'], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => $reply,
            'subscription' => $subscription
        ]);
    }

    public function processAction(\Illuminate\Http\Request $request)
    {
        $action = $request->input('action');
        $subscriptionId = $request->input('subscription_id');

        $sub = Subscription::find($subscriptionId);

        if (!$sub) {
            return response("❌ Subscription ID {$subscriptionId} not found.", 404);
        }

        if ($action === 'approve') {
            $sub->update(['status' => 'paid', 'next_due_date' => Carbon::parse($sub->next_due_date)->addMonth()]);
            return response("✅ *{$sub->subscription_title}* marked as paid!");
        } elseif ($action === 'snooze') {
            $sub->update(['next_due_date' => Carbon::parse($sub->next_due_date)->addDays(3)]);
            return response("⏰ *{$sub->subscription_title}* snoozed for 3 days.");
        } elseif ($action === 'cancel') {
            $sub->update(['status' => 'cancelled']);
            return response("❌ *{$sub->subscription_title}* flagged for cancellation.");
        }

        return response("⚠️ Invalid action type.", 400);
    }
}
