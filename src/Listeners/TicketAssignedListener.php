<?php

namespace Bithoven\Tickets\Listeners;

use Bithoven\Tickets\Events\TicketAssigned;
use Bithoven\Tickets\Mail\TicketAssigned as TicketAssignedMail;
use Bithoven\Tickets\Models\NotificationPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class TicketAssignedListener implements ShouldQueue
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
    public function handle(TicketAssigned $event): void
    {
        $ticket = $event->ticket;
        $assignedTo = $event->assignedTo;
        $assignedBy = $event->assignedBy;
        
        // Validate assignedTo has email
        if (empty($assignedTo->email)) {
            \Log::warning('Cannot send TicketAssigned email: User has no email', [
                'ticket_id' => $ticket->id,
                'user_id' => $assignedTo->id
            ]);
            return;
        }
        
        // Check if assigned user wants this notification
        $preference = NotificationPreference::forUser($assignedTo->id);
        
        if ($preference->wantsNotification('ticket_assigned')) {
            try {
                Mail::to($assignedTo->email)->send(
                    new TicketAssignedMail($ticket, $assignedTo, $assignedBy)
                );
                \Log::info('TicketAssigned email sent', [
                    'ticket_id' => $ticket->id,
                    'recipient' => $assignedTo->email
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send TicketAssigned email', [
                    'ticket_id' => $ticket->id,
                    'recipient' => $assignedTo->email,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
