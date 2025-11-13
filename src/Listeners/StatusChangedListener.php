<?php

namespace Bithoven\Tickets\Listeners;

use Bithoven\Tickets\Events\StatusChanged;
use Bithoven\Tickets\Mail\StatusChanged as StatusChangedMail;
use Bithoven\Tickets\Models\NotificationPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class StatusChangedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StatusChanged $event): void
    {
        $ticket = $event->ticket;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;
        $changedBy = $event->changedBy;
        
        // List of users to notify
        $recipients = collect();
        
        // 1. Notify ticket creator (if not the one who changed it)
        if ((!$changedBy || $ticket->user_id !== $changedBy->id) && $ticket->user && $ticket->user->email) {
            $recipients->push($ticket->user);
        }
        
        // 2. Notify assigned agent (if exists and not the one who changed it)
        if ($ticket->assigned_to && (!$changedBy || $ticket->assigned_to !== $changedBy->id)) {
            if ($ticket->assignedUser && $ticket->assignedUser->email) {
                $recipients->push($ticket->assignedUser);
            }
        }
        
        // Remove duplicates and filter empty emails
        $recipients = $recipients->unique('id')
            ->filter(function ($user) {
                return !empty($user->email);
            });
        
        // If no valid recipients, log and exit
        if ($recipients->isEmpty()) {
            \Log::info('No recipients for StatusChanged notification', [
                'ticket_id' => $ticket->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
            return;
        }
        
        // Send email to each recipient who has this notification enabled
        foreach ($recipients as $recipient) {
            $preference = NotificationPreference::forUser($recipient->id);
            
            if ($preference->wantsNotification('status_changed')) {
                try {
                    Mail::to($recipient->email)->send(
                        new StatusChangedMail($ticket, $oldStatus, $newStatus, $changedBy)
                    );
                    \Log::info('StatusChanged email sent', [
                        'ticket_id' => $ticket->id,
                        'recipient' => $recipient->email,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send StatusChanged email', [
                        'ticket_id' => $ticket->id,
                        'recipient' => $recipient->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }
}
