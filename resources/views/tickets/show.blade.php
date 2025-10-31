@extends('layouts._default')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="container-fluid">
    {{-- Header with Actions --}}
    <div class="row mb-5">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light mb-3">
                                <i class="fas fa-arrow-left"></i> Back to Tickets
                            </a>
                            <h1 class="mb-2">
                                <i class="fas fa-ticket-alt text-primary me-2"></i>
                                Ticket #{{ $ticket->ticket_number }}
                            </h1>
                            <h3 class="text-gray-700 mb-3">{{ $ticket->subject }}</h3>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge badge-light-{{ $ticket->status_color }} fs-6 px-3 py-2">
                                    {{ $ticket->status_label }}
                                </span>
                                <span class="badge badge-{{ $ticket->priority_color }} fs-6 px-3 py-2">
                                    {{ $ticket->priority_label }}
                                </span>
                                @if($ticket->category)
                                <span class="badge fs-6 px-3 py-2" style="background-color: {{ $ticket->category->color }}; color: white;">
                                    <i class="fas {{ $ticket->category->icon }} me-1"></i>
                                    {{ $ticket->category->name }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @can('update', $ticket)
                            @if(!$ticket->isClosed())
                            <button type="button" class="btn btn-danger" onclick="closeTicket()">
                                <i class="fas fa-times-circle"></i> Close
                            </button>
                            @else
                            <button type="button" class="btn btn-success" onclick="reopenTicket()">
                                <i class="fas fa-redo"></i> Reopen
                            </button>
                            @endif
                            @endcan
                            @can('assign', \Bithoven\Tickets\Models\Ticket::class)
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignModal">
                                <i class="fas fa-user-plus"></i> Assign
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Ticket Description --}}
            <div class="card shadow-sm mb-5">
                <div class="card-header">
                    <h3 class="card-title">Description</h3>
                </div>
                <div class="card-body">
                    <div class="text-gray-700 fs-6" style="white-space: pre-wrap;">{{ $ticket->description }}</div>
                </div>
            </div>

            {{-- Comments --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-comments me-2"></i>
                        Comments ({{ $ticket->comments->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    @if($ticket->comments->count() > 0)
                    <div class="timeline">
                        @foreach($ticket->comments as $comment)
                        <div class="timeline-item mb-5">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                @if($comment->user->avatar)
                                <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}">
                                @else
                                <span class="symbol-label bg-light-primary text-primary fw-semibold">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </span>
                                @endif
                            </div>
                            <div class="timeline-content mb-10 mt-n1">
                                <div class="pe-3 mb-5">
                                    <div class="fs-5 fw-semibold mb-2">
                                        {{ $comment->user->name }}
                                        @if($comment->is_internal)
                                        <span class="badge badge-light-warning ms-2">Internal Note</span>
                                        @endif
                                        @if($comment->is_solution)
                                        <span class="badge badge-light-success ms-2">
                                            <i class="fas fa-check"></i> Solution
                                        </span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center mt-1 fs-6">
                                        <div class="text-muted me-2 fs-7">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="overflow-auto pb-5">
                                    <div class="text-gray-800" style="white-space: pre-wrap;">{{ $comment->comment }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-10">
                        <i class="fas fa-comments fs-3x text-muted mb-3"></i>
                        <p class="text-muted">No comments yet</p>
                    </div>
                    @endif

                    {{-- Add Comment Form --}}
                    @can('view', $ticket)
                    <div class="separator my-5"></div>
                    <form action="{{ route('tickets.comments.store', $ticket) }}" method="POST" id="commentForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Add Comment</label>
                            <textarea name="comment" class="form-control" rows="4" placeholder="Type your comment..." required></textarea>
                        </div>
                        @can('addInternalComment', $ticket)
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_internal" id="isInternal" value="1">
                            <label class="form-check-label" for="isInternal">
                                Internal Note (not visible to customer)
                            </label>
                        </div>
                        @endcan
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Post Comment
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Ticket Information --}}
            <div class="card shadow-sm mb-5">
                <div class="card-header">
                    <h3 class="card-title">Ticket Information</h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">Created By</div>
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
                            <div>
                                <span class="fw-semibold text-gray-800 d-block">{{ $ticket->user->name }}</span>
                                <span class="text-muted fs-7">{{ $ticket->user->email }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="separator my-4"></div>

                    <div class="mb-4">
                        <div class="text-muted fs-7 mb-1">Assigned To</div>
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
                            <div>
                                <span class="fw-semibold text-gray-800 d-block">{{ $ticket->assignedUser->name }}</span>
                                <span class="text-muted fs-7">{{ $ticket->assignedUser->email }}</span>
                            </div>
                        </div>
                        @else
                        <span class="badge badge-light-warning">Unassigned</span>
                        @endif
                    </div>

                    <div class="separator my-4"></div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted fs-7 mb-1">Created</div>
                            <div class="fw-semibold text-gray-800">{{ $ticket->created_at->format('M d, Y') }}</div>
                            <div class="text-muted fs-8">{{ $ticket->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted fs-7 mb-1">Updated</div>
                            <div class="fw-semibold text-gray-800">{{ $ticket->updated_at->format('M d, Y') }}</div>
                            <div class="text-muted fs-8">{{ $ticket->updated_at->diffForHumans() }}</div>
                        </div>
                        @if($ticket->resolved_at)
                        <div class="col-6">
                            <div class="text-muted fs-7 mb-1">Resolved</div>
                            <div class="fw-semibold text-gray-800">{{ $ticket->resolved_at->format('M d, Y') }}</div>
                        </div>
                        @endif
                        @if($ticket->closed_at)
                        <div class="col-6">
                            <div class="text-muted fs-7 mb-1">Closed</div>
                            <div class="fw-semibold text-gray-800">{{ $ticket->closed_at->format('M d, Y') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Attachments --}}
            @if($ticket->attachments->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paperclip me-2"></i>
                        Attachments ({{ $ticket->attachments->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    @foreach($ticket->attachments as $attachment)
                    <div class="d-flex align-items-center mb-3">
                        <div class="symbol symbol-40px me-3">
                            <span class="symbol-label bg-light-primary">
                                <i class="fas fa-file text-primary fs-5"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <a href="{{ $attachment->url }}" target="_blank" class="text-gray-800 text-hover-primary fw-semibold d-block">
                                {{ $attachment->original_filename }}
                            </a>
                            <span class="text-muted fs-7">{{ $attachment->formatted_size }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Assign Modal --}}
@can('assign', \Bithoven\Tickets\Models\Ticket::class)
<div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('tickets.assign', $ticket) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Assign to Agent</label>
                        <select name="assigned_to" class="form-select" required>
                            <option value="">Select agent...</option>
                            @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $ticket->assigned_to == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
function closeTicket() {
    if (confirm('Are you sure you want to close this ticket?')) {
        fetch('{{ route("tickets.close", $ticket) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function reopenTicket() {
    if (confirm('Are you sure you want to reopen this ticket?')) {
        fetch('{{ route("tickets.reopen", $ticket) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection
