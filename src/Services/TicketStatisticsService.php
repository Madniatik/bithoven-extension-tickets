<?php

namespace Bithoven\Tickets\Services;

use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TicketStatisticsService
{
    /**
     * Cache duration in minutes
     */
    protected int $cacheDuration = 30;

    /**
     * Get all dashboard statistics
     */
    public function getAllStatistics(): array
    {
        return Cache::remember('tickets.dashboard.stats', $this->cacheDuration, function () {
            return [
                'overview' => $this->getOverviewStats(),
                'byStatus' => $this->getTicketsByStatus(),
                'byCategory' => $this->getTicketsByCategory(),
                'byPriority' => $this->getTicketsByPriority(),
                'timeline' => $this->getTicketsTimeline(),
                'averageResolutionTime' => $this->getAverageResolutionTime(),
                'agentPerformance' => $this->getAgentPerformance(),
                'recentActivity' => $this->getRecentActivity(),
            ];
        });
    }

    /**
     * Get overview statistics
     */
    public function getOverviewStats(): array
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::whereIn('status', ['new', 'open', 'in_progress'])->count();
        $closedToday = Ticket::where('status', 'closed')
            ->whereDate('updated_at', Carbon::today())
            ->count();
        $pendingAssignment = Ticket::whereNull('assigned_to')
            ->where('status', '!=', 'closed')
            ->count();

        return [
            'total' => $totalTickets,
            'open' => $openTickets,
            'closed_today' => $closedToday,
            'pending_assignment' => $pendingAssignment,
            'resolution_rate' => $totalTickets > 0 
                ? round((Ticket::where('status', 'closed')->count() / $totalTickets) * 100, 2) 
                : 0,
        ];
    }

    /**
     * Get tickets count by status
     */
    public function getTicketsByStatus(): array
    {
        $statuses = ['new', 'open', 'in_progress', 'pending', 'on_hold', 'resolved', 'closed'];
        $data = [];

        foreach ($statuses as $status) {
            $count = Ticket::where('status', $status)->count();
            $data[$status] = [
                'label' => ucfirst(str_replace('_', ' ', $status)),
                'count' => $count,
                'color' => $this->getStatusColor($status),
            ];
        }

        return $data;
    }

    /**
     * Get tickets count by category
     */
    public function getTicketsByCategory(): array
    {
        return TicketCategory::withCount('tickets')
            ->where('is_active', true)
            ->orderBy('tickets_count', 'desc')
            ->get()
            ->map(function ($category) {
                return [
                    'label' => $category->name,
                    'count' => $category->tickets_count,
                    'color' => $category->color ?? '#3b82f6',
                ];
            })
            ->toArray();
    }

    /**
     * Get tickets count by priority
     */
    public function getTicketsByPriority(): array
    {
        $priorities = ['low', 'medium', 'high', 'critical'];
        $data = [];

        foreach ($priorities as $priority) {
            $count = Ticket::where('priority', $priority)->count();
            $data[$priority] = [
                'label' => ucfirst($priority),
                'count' => $count,
                'color' => $this->getPriorityColor($priority),
            ];
        }

        return $data;
    }

    /**
     * Get tickets created over time (last 30 days)
     */
    public function getTicketsTimeline(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);
        
        $tickets = Ticket::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Fill missing dates with zero
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - $i - 1)->format('Y-m-d');
            $ticket = $tickets->firstWhere('date', $date);
            $data[] = [
                'date' => Carbon::parse($date)->format('M d'),
                'count' => $ticket ? $ticket->count : 0,
            ];
        }

        return $data;
    }

    /**
     * Calculate average resolution time in hours
     */
    public function getAverageResolutionTime(): array
    {
        $closedTickets = Ticket::where('status', 'closed')
            ->whereNotNull('updated_at')
            ->get();

        if ($closedTickets->isEmpty()) {
            return [
                'hours' => 0,
                'formatted' => '0h 0m',
            ];
        }

        $totalMinutes = 0;
        foreach ($closedTickets as $ticket) {
            $totalMinutes += $ticket->created_at->diffInMinutes($ticket->updated_at);
        }

        $averageMinutes = $totalMinutes / $closedTickets->count();
        $hours = floor($averageMinutes / 60);
        $minutes = $averageMinutes % 60;

        return [
            'hours' => round($averageMinutes / 60, 2),
            'formatted' => sprintf('%dh %dm', $hours, $minutes),
        ];
    }

    /**
     * Get agent performance metrics
     */
    public function getAgentPerformance(): array
    {
        $agents = Ticket::select('assigned_to', DB::raw('COUNT(*) as total_tickets'))
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->with('assignedUser:id,name,avatar')
            ->get();

        return $agents->map(function ($agent) {
            $closedTickets = Ticket::where('assigned_to', $agent->assigned_to)
                ->where('status', 'closed')
                ->count();

            return [
                'name' => $agent->assignedUser->name ?? 'Unknown',
                'avatar' => $agent->assignedUser->avatar ?? null,
                'total_tickets' => $agent->total_tickets,
                'closed_tickets' => $closedTickets,
                'completion_rate' => $agent->total_tickets > 0 
                    ? round(($closedTickets / $agent->total_tickets) * 100, 2) 
                    : 0,
            ];
        })->toArray();
    }

    /**
     * Get recent activity (last 10 tickets)
     */
    public function getRecentActivity(): array
    {
        return Ticket::with(['user:id,name,avatar', 'assignedUser:id,name', 'category:id,name,color'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'category' => $ticket->category?->name,
                    'category_color' => $ticket->category?->color,
                    'creator' => $ticket->user?->name,
                    'assigned_to' => $ticket->assignedUser?->name,
                    'created_at' => $ticket->created_at->diffForHumans(),
                ];
            })
            ->toArray();
    }

    /**
     * Clear statistics cache
     */
    public function clearCache(): void
    {
        Cache::forget('tickets.dashboard.stats');
    }

    /**
     * Get color for ticket status
     */
    protected function getStatusColor(string $status): string
    {
        return match($status) {
            'new' => '#3b82f6',      // blue
            'open' => '#10b981',     // green
            'in_progress' => '#f59e0b', // orange
            'pending' => '#eab308',  // yellow
            'on_hold' => '#6b7280',  // gray
            'resolved' => '#8b5cf6', // purple
            'closed' => '#6b7280',   // gray
            default => '#3b82f6',
        };
    }

    /**
     * Get color for ticket priority
     */
    protected function getPriorityColor(string $priority): string
    {
        return match($priority) {
            'low' => '#10b981',      // green
            'medium' => '#f59e0b',   // orange
            'high' => '#ef4444',     // red
            'critical' => '#dc2626', // dark red
            default => '#3b82f6',
        };
    }
}
