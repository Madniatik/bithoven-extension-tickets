<x-default-layout>
    @section('title', 'Tickets')

    @section('breadcrumbs')
        {{ Breadcrumbs::render('tickets.index') }}
    @endsection

    @push('styles')
    <style>
        .ticket-status-badge {
            padding: 0.5rem 1rem;
            border-radius: 0.475rem;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .ticket-priority-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.85rem;
        }
    </style>
    @endpush
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row mb-5">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="mb-2">
                                <i class="fas fa-ticket-alt text-primary me-2"></i>
                                Support Tickets
                            </h1>
                            <p class="text-muted mb-0">Manage and track support tickets</p>
                        </div>
                        @can('create-tickets')
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus"></i> New Ticket
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-5 mb-5">
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-primary">
                                <i class="fas fa-inbox fs-2x text-primary"></i>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-700 fw-semibold d-block fs-6">Total</span>
                            <span class="text-gray-400 fw-semibold d-block fs-7">All Tickets</span>
                        </div>
                    </div>
                    <div class="fs-2x fw-bold text-gray-800">{{ $statistics['total'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-success">
                                <i class="fas fa-folder-open fs-2x text-success"></i>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-700 fw-semibold d-block fs-6">Open</span>
                            <span class="text-gray-400 fw-semibold d-block fs-7">Active Tickets</span>
                        </div>
                    </div>
                    <div class="fs-2x fw-bold text-gray-800">{{ $statistics['open'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-warning">
                                <i class="fas fa-exclamation-triangle fs-2x text-warning"></i>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-700 fw-semibold d-block fs-6">Urgent</span>
                            <span class="text-gray-400 fw-semibold d-block fs-7">High Priority</span>
                        </div>
                    </div>
                    <div class="fs-2x fw-bold text-gray-800">{{ $statistics['urgent'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="symbol symbol-50px me-3">
                            <span class="symbol-label bg-light-info">
                                <i class="fas fa-user-slash fs-2x text-info"></i>
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-700 fw-semibold d-block fs-6">Unassigned</span>
                            <span class="text-gray-400 fw-semibold d-block fs-7">Need Assignment</span>
                        </div>
                    </div>
                    <div class="fs-2x fw-bold text-gray-800">{{ $statistics['unassigned'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <form method="GET" action="{{ route('tickets.index') }}" id="filtersForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Ticket #, subject..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="">All Priorities</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">All Assignments</option>
                            <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('assigned_to') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-light">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tickets Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            @if($tickets->count() > 0)
            <div class="table-responsive">
                <table class="table table-row-bordered table-hover align-middle gs-7">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                            <th>Ticket #</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Category</th>
                            <th>Created By</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-gray-800 text-hover-primary fw-bold">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-gray-800 text-hover-primary">
                                    {{ Str::limit($ticket->subject, 50) }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-light-{{ $ticket->status_color }} ticket-status-badge">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $ticket->priority_color }} ticket-priority-badge">
                                    {{ $ticket->priority_label }}
                                </span>
                            </td>
                            <td>
                                @if($ticket->category)
                                <span class="badge" style="background-color: {{ $ticket->category->color }}; color: white;">
                                    <i class="fas {{ $ticket->category->icon }} me-1"></i>
                                    {{ $ticket->category->name }}
                                </span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px symbol-circle me-3">
                                        @if($ticket->user->avatar)
                                        <img src="{{ asset('storage/' . $ticket->user->avatar) }}" alt="{{ $ticket->user->name }}">
                                        @else
                                        <span class="symbol-label bg-light-primary text-primary fw-semibold">
                                            {{ substr($ticket->user->name, 0, 1) }}
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-gray-800">{{ $ticket->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($ticket->assignedUser)
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px symbol-circle me-3">
                                        @if($ticket->assignedUser->avatar)
                                        <img src="{{ asset('storage/' . $ticket->assignedUser->avatar) }}" alt="{{ $ticket->assignedUser->name }}">
                                        @else
                                        <span class="symbol-label bg-light-success text-success fw-semibold">
                                            {{ substr($ticket->assignedUser->name, 0, 1) }}
                                        </span>
                                        @endif
                                    </div>
                                    <span class="text-gray-800">{{ $ticket->assignedUser->name }}</span>
                                </div>
                                @else
                                <span class="badge badge-light-warning">Unassigned</span>
                                @endif
                            </td>
                            <td>{{ $ticket->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-light btn-active-light-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-5">
                <div class="text-muted">
                    Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} tickets
                </div>
                <div>
                    {{ $tickets->links() }}
                </div>
            </div>
            @else
            <div class="text-center py-10">
                <i class="fas fa-inbox fs-3x text-muted mb-5"></i>
                <p class="text-muted fs-4">No tickets found</p>
                @can('create-tickets')
                <a href="{{ route('tickets.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> Create Your First Ticket
                </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>
</x-default-layout>
