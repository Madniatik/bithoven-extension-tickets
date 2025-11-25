<?php

namespace Bithoven\Tickets\Http\Controllers;

use App\Http\Controllers\Controller;
use Bithoven\Tickets\Services\TicketStatisticsService;

class TicketDashboardController extends Controller
{
    protected TicketStatisticsService $statisticsService;

    public function __construct(TicketStatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Display the ticket dashboard
     * Only accessible to users with automation:manage permission (admin/support staff)
     */
    public function index()
    {
        // Check permission - only admin/support staff can view dashboard
        if (!auth()->user()->can('extensions:tickets:automation:manage')) {
            abort(403, 'No tienes permiso para ver el dashboard de tickets. Solo administradores y personal de soporte tienen acceso.');
        }

        // Get all statistics
        $stats = $this->statisticsService->getAllStatistics();

        return view('tickets::dashboard', [
            'stats' => $stats,
            'pageTitle' => 'Dashboard de Tickets',
        ]);
    }

    /**
     * Refresh dashboard statistics (AJAX)
     */
    public function refresh()
    {
        // Check permission
        if (!auth()->user()->can('extensions:tickets:automation:manage')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para refrescar el dashboard.',
            ], 403);
        }

        // Clear cache and get fresh stats
        $this->statisticsService->clearCache();
        $stats = $this->statisticsService->getAllStatistics();

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }
}
