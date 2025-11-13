<?php

namespace Bithoven\Tickets\Listeners;

use Bithoven\Tickets\Events\PriorityEscalated;
use Bithoven\Tickets\Mail\PriorityEscalated as PriorityEscalatedMail;
use Bithoven\Tickets\Models\NotificationPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class PriorityEscalatedListener implements ShouldQueue
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
    public function handle(PriorityEscalated $event): void
    {
        $ticket = $event->ticket;
        $oldPriority = $event->oldPriority;
        $newPriority = $event->newPriority;
        
        // Only notify for escalations to High or Critical priority
        if (!in_array($newPriority, ['high', 'critical'])) {
            return;
        }
        
        // Get all admins (users with edit-tickets permission) with valid emails
        try {
            $admins = User::permission('edit-tickets')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();
        } catch (\Exception $e) {
            \Log::warning('Could not fetch admins for PriorityEscalated notification', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
            $admins = collect();
        }
        
        // Filter empty emails
        $recipients = $admins->filter(function ($user) {
            return !empty($user->email);
        });
        
        // If no valid recipients, log and exit
        if ($recipients->isEmpty()) {
            \Log::info('No recipients for PriorityEscalated notification', [
                'ticket_id' => $ticket->id,
                'old_priority' => $oldPriority,
                'new_priority' => $newPriority,
                'reason' => 'No admins found with edit-tickets permission'
            ]);
            return;
        }
        
        // Send email to each admin who has this notification enabled
        foreach ($recipients as $admin) {
            $preference = NotificationPreference::forUser($admin->id);
            
            if ($preference->wantsNotification('priority_escalated')) {
                try {
                    Mail::to($admin->email)->send(
                        new PriorityEscalatedMail($ticket, $oldPriority, $newPriority)
                    );
                    \Log::info('PriorityEscalated email sent', [
                        'ticket_id' => $ticket->id,
                        'recipient' => $admin->email,
                        'old_priority' => $oldPriority,
                        'new_priority' => $newPriority
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send PriorityEscalated email', [
                        'ticket_id' => $ticket->id,
                        'recipient' => $admin->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }
}
