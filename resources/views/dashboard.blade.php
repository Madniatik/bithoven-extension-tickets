<x-default-layout>
    @section('title', 'Dashboard de Tickets')
    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.dashboard') }}
    @endsection

    {{-- Overview Cards --}}
    <div class="row g-5 g-xl-8 mb-5">
        {{-- Total Tickets --}}
        <div class="col-xl-3">
            <div class="card card-flush h-xl-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6">Total Tickets</div>
                        <i class="ki-duotone ki-notepad fs-2x text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                    <div class="d-flex align-items-center pt-3">
                        <span class="fs-2hx fw-bold text-gray-900 me-2">{{ $stats['overview']['total'] }}</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7">
                        Tasa de resolución: {{ $stats['overview']['resolution_rate'] }}%
                    </div>
                </div>
            </div>
        </div>

        {{-- Open Tickets --}}
        <div class="col-xl-3">
            <div class="card card-flush h-xl-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6">Tickets Abiertos</div>
                        <i class="ki-duotone ki-folder-open fs-2x text-success">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <div class="d-flex align-items-center pt-3">
                        <span class="fs-2hx fw-bold text-gray-900 me-2">{{ $stats['overview']['open'] }}</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7">
                        Requieren atención inmediata
                    </div>
                </div>
            </div>
        </div>

        {{-- Closed Today --}}
        <div class="col-xl-3">
            <div class="card card-flush h-xl-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6">Cerrados Hoy</div>
                        <i class="ki-duotone ki-check-circle fs-2x text-info">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <div class="d-flex align-items-center pt-3">
                        <span class="fs-2hx fw-bold text-gray-900 me-2">{{ $stats['overview']['closed_today'] }}</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7">
                        Productividad del día
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending Assignment --}}
        <div class="col-xl-3">
            <div class="card card-flush h-xl-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex flex-stack">
                        <div class="text-gray-700 fw-semibold fs-6">Pendientes de Asignar</div>
                        <i class="ki-duotone ki-user-tick fs-2x text-warning">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </div>
                    <div class="d-flex align-items-center pt-3">
                        <span class="fs-2hx fw-bold text-gray-900 me-2">{{ $stats['overview']['pending_assignment'] }}</span>
                    </div>
                    <div class="fw-semibold text-gray-500 fs-7">
                        Requieren asignación
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-5 g-xl-8 mb-5">
        {{-- Tickets by Status --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Tickets por Estado</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Distribución actual</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>

        {{-- Tickets by Priority --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Tickets por Prioridad</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Distribución de urgencia</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <canvas id="priorityChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-5 g-xl-8 mb-5">
        {{-- Tickets Timeline --}}
        <div class="col-xl-8">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Tickets Creados (Últimos 30 Días)</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Tendencia temporal</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <canvas id="timelineChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Average Resolution Time --}}
        <div class="col-xl-4">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Tiempo Promedio de Resolución</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Métricas de performance</span>
                    </h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center pt-10">
                    <div class="text-center">
                        <span class="fs-3hx fw-bold text-primary">{{ $stats['averageResolutionTime']['formatted'] }}</span>
                        <div class="fw-semibold text-gray-500 fs-6 mt-2">Tiempo promedio</div>
                    </div>
                    <div class="mt-8 text-gray-700">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ki-duotone ki-time fs-2 text-success me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span>{{ $stats['averageResolutionTime']['hours'] }} horas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tickets by Category --}}
    @if(count($stats['byCategory']) > 0)
    <div class="row g-5 g-xl-8 mb-5">
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Tickets por Categoría</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Top categorías más usadas</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <canvas id="categoryChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Agent Performance --}}
    @if(count($stats['agentPerformance']) > 0)
    <div class="row g-5 g-xl-8 mb-5">
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Performance de Agentes</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Métricas de productividad</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-gray-700">
                                    <th>Agente</th>
                                    <th class="text-end">Total Tickets</th>
                                    <th class="text-end">Cerrados</th>
                                    <th class="text-end">Tasa Completado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['agentPerformance'] as $agent)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($agent['avatar'])
                                                <div class="symbol symbol-30px me-3">
                                                    <img src="{{ image($agent['avatar']) }}" alt="{{ $agent['name'] }}">
                                                </div>
                                            @endif
                                            <div class="fw-bold">{{ $agent['name'] }}</div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold">{{ $agent['total_tickets'] }}</td>
                                    <td class="text-end fw-semibold text-success">{{ $agent['closed_tickets'] }}</td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <span class="badge badge-light-success fs-7 fw-bold">{{ $agent['completion_rate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Recent Activity --}}
    <div class="row g-5 g-xl-8">
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">Actividad Reciente</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">Últimos 10 tickets</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light-primary">
                            Ver Todos
                        </a>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-gray-700">
                                    <th>Ticket</th>
                                    <th>Asunto</th>
                                    <th>Estado</th>
                                    <th>Prioridad</th>
                                    <th>Categoría</th>
                                    <th>Creado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recentActivity'] as $ticket)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $ticket['ticket_number'] }}</span>
                                    </td>
                                    <td>
                                        <div class="text-gray-900 fw-bold text-hover-primary">
                                            {{ \Str::limit($ticket['subject'], 40) }}
                                        </div>
                                        <div class="text-gray-500 fs-7">Por: {{ $ticket['creator'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $ticket['status'] === 'closed' ? 'secondary' : ($ticket['status'] === 'open' ? 'success' : 'warning') }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket['status'])) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $ticket['priority'] === 'critical' ? 'danger' : ($ticket['priority'] === 'high' ? 'warning' : ($ticket['priority'] === 'medium' ? 'info' : 'success')) }}">
                                            {{ ucfirst($ticket['priority']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ticket['category'])
                                            <span class="badge" style="background-color: {{ $ticket['category_color'] }}; color: white;">
                                                {{ $ticket['category'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-600">{{ $ticket['created_at'] }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('tickets.show', $ticket['id']) }}" class="btn btn-sm btn-light-primary">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart.js default configuration
        Chart.defaults.font.family = 'Inter, sans-serif';
        Chart.defaults.font.size = 13;
        Chart.defaults.color = '#6c757d';

        // Status Chart (Donut)
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const statusData = @json($stats['byStatus']);
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.values(statusData).map(s => s.label),
                    datasets: [{
                        data: Object.values(statusData).map(s => s.count),
                        backgroundColor: Object.values(statusData).map(s => s.color),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                            }
                        }
                    }
                }
            });
        }

        // Priority Chart (Pie)
        const priorityCtx = document.getElementById('priorityChart');
        if (priorityCtx) {
            const priorityData = @json($stats['byPriority']);
            new Chart(priorityCtx, {
                type: 'pie',
                data: {
                    labels: Object.values(priorityData).map(p => p.label),
                    datasets: [{
                        data: Object.values(priorityData).map(p => p.count),
                        backgroundColor: Object.values(priorityData).map(p => p.color),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                            }
                        }
                    }
                }
            });
        }

        // Timeline Chart (Line)
        const timelineCtx = document.getElementById('timelineChart');
        if (timelineCtx) {
            const timelineData = @json($stats['timeline']);
            new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels: timelineData.map(t => t.date),
                    datasets: [{
                        label: 'Tickets Creados',
                        data: timelineData.map(t => t.count),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#3b82f6',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // Category Chart (Bar)
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            const categoryData = @json($stats['byCategory']);
            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: categoryData.map(c => c.label),
                    datasets: [{
                        label: 'Tickets',
                        data: categoryData.map(c => c.count),
                        backgroundColor: categoryData.map(c => c.color),
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    });
    </script>
    @endpush
</x-default-layout>
