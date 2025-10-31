@extends('layouts._default')

@section('title', 'Create New Ticket')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            {{-- Header --}}
            <div class="card shadow-sm mb-5">
                <div class="card-body">
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-light mb-3">
                        <i class="fas fa-arrow-left"></i> Back to Tickets
                    </a>
                    <h1 class="mb-2">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Create New Ticket
                    </h1>
                    <p class="text-muted mb-0">Submit a new support request</p>
                </div>
            </div>

            {{-- Form --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Subject --}}
                        <div class="mb-5">
                            <label class="form-label required">Subject</label>
                            <input type="text" 
                                   name="subject" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   placeholder="Brief description of your issue"
                                   value="{{ old('subject') }}"
                                   required>
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-5">
                            <label class="form-label required">Description</label>
                            <textarea name="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="8"
                                      placeholder="Please provide detailed information about your issue"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Be as detailed as possible to help us resolve your issue faster</div>
                        </div>

                        <div class="row g-5 mb-5">
                            {{-- Priority --}}
                            <div class="col-md-6">
                                <label class="form-label required">Priority</label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="">Select priority...</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select category...</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Attachments --}}
                        @if(config('tickets.uploads.enabled'))
                        <div class="mb-5">
                            <label class="form-label">Attachments</label>
                            <input type="file" 
                                   name="attachments[]" 
                                   class="form-control @error('attachments.*') is-invalid @enderror" 
                                   multiple
                                   accept=".{{ implode(',.' , config('tickets.uploads.allowed_types')) }}">
                            @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Max {{ config('tickets.uploads.max_size') / 1024 }}MB per file. 
                                Allowed types: {{ implode(', ', config('tickets.uploads.allowed_types')) }}
                            </div>
                        </div>
                        @endif

                        {{-- Assign (staff only) --}}
                        @can('assign-tickets')
                        <div class="mb-5">
                            <label class="form-label">Assign To (Optional)</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('assigned_to') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endcan

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('tickets.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
