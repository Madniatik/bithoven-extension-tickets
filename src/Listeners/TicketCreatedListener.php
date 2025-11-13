<?php

namespace Bithoven\Tickets\Listeners;

use Bithoven\Tickets\Events\TicketCreated;
use Bithoven\Tickets\Mail\TicketCreated as TicketCreatedMail;
use Bithoven\Tickets\Models\NotificationPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TicketCreatedListener implements ShouldQueue
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
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;
        
        // Only notify the assigned user (if exists and has email)
        if (!$ticket->assigned_to) {
            \Log::info('No recipient for TicketCreated notification: ticket not assigned', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number
            ]);
            return;
        }
        
        $assignedUser = User::find($ticket->assigned_to);
        
        if (!$assignedUser || empty($assignedUser->email)) {
            \Log::warning('Cannot send TicketCreated email: assigned user has no email', [
                'ticket_id' => $ticket->id,
                'assigned_to' => $ticket->assigned_to
            ]);
            return;
        }
        
        // Check if assigned user wants this notification
        $preference = NotificationPreference::forUser($assignedUser->id);
        
        if ($preference->wantsNotification('ticket_created')) {
            try {
                Mail::to($assignedUser->email)->send(new TicketCreatedMail($ticket));
                \Log::info('TicketCreated email sent', [
                    'ticket_id' => $ticket->id,
                    'recipient' => $assignedUser->email
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send TicketCreated email', [
                    'ticket_id' => $ticket->id,
                    'recipient' => $assignedUser->email,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
