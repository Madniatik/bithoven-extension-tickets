<?php

namespace Bithoven\Tickets\Policies;

use App\Core\Foundation\Models\User;
use Bithoven\Tickets\Models\Ticket;

class TicketPolicy
{
    /**
     * Determine if user can view any tickets
     */
    public function viewAny(User $user): bool
    {
        return $user->can('extensions:tickets:base:view');
    }

    /**
     * Determine if user can view the ticket
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // User can view if they have permission and either:
        // - They created the ticket
        // - They are assigned to it
        // - They have edit permission (support staff)
        return $user->can('extensions:tickets:base:view') && (
            $ticket->user_id === $user->id ||
            $ticket->assigned_to === $user->id ||
            $user->can('extensions:tickets:base:edit')
        );
    }

    /**
     * Determine if user can create tickets
     */
    public function create(User $user): bool
    {
        return $user->can('extensions:tickets:base:create');
    }

    /**
     * Determine if the user can update the ticket (full edit - solo creador).
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->can('extensions:tickets:base:edit') && $ticket->user_id === $user->id;
    }

    /**
     * Determine if the user can update the ticket status (creador, asignado, o admin).
     */
    public function updateStatus(User $user, Ticket $ticket): bool
    {
        // Puede cambiar el estado: SOLO usuario asignado O quien tenga categories:manage (admins)
        // El creador NO puede cambiar el estado, solo comentar y eliminar
        return $user->can('extensions:tickets:base:edit') && (
            $ticket->assigned_to === $user->id ||
            $user->can('extensions:tickets:categories:manage')
        );
    }

    /**
     * Determine if user can delete the ticket
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // User must have delete permission and either:
        // - They created the ticket
        // - They are assigned to it
        // - They have categories:manage permission (admin/super-admin)
        return $user->can('extensions:tickets:base:delete') && (
            $ticket->user_id === $user->id ||
            $ticket->assigned_to === $user->id ||
            $user->can('extensions:tickets:categories:manage')
        );
    }

    /**
     * Determine if user can assign tickets
     */
    public function assign(User $user): bool
    {
        return $user->can('extensions:tickets:automation:manage');
    }

    /**
     * Determine if user can add internal comments
     */
    public function addInternalComment(User $user): bool
    {
        return $user->can('extensions:tickets:base:edit');
    }
}
