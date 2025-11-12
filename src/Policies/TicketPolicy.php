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
        return $user->can('view-tickets');
    }

    /**
     * Determine if user can view the ticket
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // User can view if they have permission and either:
        // - They created the ticket
        // - They are assigned to it
        // - They have edit-tickets permission (support staff)
        return $user->can('view-tickets') && (
            $ticket->user_id === $user->id ||
            $ticket->assigned_to === $user->id ||
            $user->can('edit-tickets')
        );
    }

    /**
     * Determine if user can create tickets
     */
    public function create(User $user): bool
    {
        return $user->can('create-tickets');
    }

    /**
     * Determine if the user can update the ticket (full edit - solo creador).
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->can('edit-tickets') && $ticket->user_id === $user->id;
    }

    /**
     * Determine if the user can update the ticket status (creador, asignado, o admin).
     */
    public function updateStatus(User $user, Ticket $ticket): bool
    {
        // Puede cambiar el estado: SOLO usuario asignado O quien tenga manage-ticket-categories (admins)
        // El creador NO puede cambiar el estado, solo comentar y eliminar
        return $user->can('edit-tickets') && (
            $ticket->assigned_to === $user->id ||
            $user->can('manage-ticket-categories')
        );
    }

    /**
     * Determine if user can delete the ticket
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // User must have delete-tickets permission and either:
        // - They created the ticket
        // - They are assigned to it
        // - They have manage-ticket-categories permission (admin/super-admin)
        return $user->can('delete-tickets') && (
            $ticket->user_id === $user->id ||
            $ticket->assigned_to === $user->id ||
            $user->can('manage-ticket-categories')
        );
    }

    /**
     * Determine if user can assign tickets
     */
    public function assign(User $user): bool
    {
        return $user->can('assign-tickets');
    }

    /**
     * Determine if user can add internal comments
     */
    public function addInternalComment(User $user): bool
    {
        return $user->can('edit-tickets');
    }
}
