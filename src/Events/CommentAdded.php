<?php

namespace Bithoven\Tickets\Events;

use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketComment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Ticket $ticket,
        public TicketComment $comment
    ) {
        //
    }
}
