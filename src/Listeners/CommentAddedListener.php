<?php

namespace Bithoven\Tickets\Listeners;

use Bithoven\Tickets\Events\CommentAdded;
use Bithoven\Tickets\Mail\CommentAdded as CommentAddedMail;
use Bithoven\Tickets\Models\NotificationPreference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CommentAddedListener implements ShouldQueue
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
    public function handle(CommentAdded $event): void
    {
        $ticket = $event->ticket;
        $comment = $event->comment;
        
        // Don't notify for internal comments
        if ($comment->is_internal) {
            return;
        }
        
        // List of users to notify
        $recipients = collect();
        
        // 1. Notify ticket creator (if not the commenter)
        if ($ticket->user_id !== $comment->user_id && $ticket->user && $ticket->user->email) {
            $recipients->push($ticket->user);
        }
        
        // 2. Notify assigned agent (if exists and not the commenter)
        if ($ticket->assigned_to && $ticket->assigned_to !== $comment->user_id) {
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
            \Log::info('No recipients for CommentAdded notification', [
                'ticket_id' => $ticket->id,
                'comment_id' => $comment->id
            ]);
            return;
        }
        
        // Send email to each recipient who has this notification enabled
        foreach ($recipients as $recipient) {
            $preference = NotificationPreference::forUser($recipient->id);
            
            if ($preference->wantsNotification('comment_added')) {
                try {
                    Mail::to($recipient->email)->send(new CommentAddedMail($ticket, $comment));
                    \Log::info('CommentAdded email sent', [
                        'ticket_id' => $ticket->id,
                        'comment_id' => $comment->id,
                        'recipient' => $recipient->email
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send CommentAdded email', [
                        'ticket_id' => $ticket->id,
                        'comment_id' => $comment->id,
                        'recipient' => $recipient->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }
}
