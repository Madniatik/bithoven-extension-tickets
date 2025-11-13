<?php

namespace Bithoven\Tickets\Events;

use Bithoven\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PriorityEscalated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Ticket $ticket,
        public string $oldPriority,
        public string $newPriority,
        public ?User $changedBy = null
    ) {
        //
    }
}
