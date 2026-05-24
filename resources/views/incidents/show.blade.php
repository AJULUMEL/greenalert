@extends('adminlte::page')

@section('title', 'Incident Details - GreenAlert')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Incident #{{ $incident->id }}</h1>
        <div>
            <a href="{{ route('incidents.edit', $incident->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('incidents.destroy', $incident->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
            <a href="{{ route('incidents.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Incident Details -->
        <div class="col-md-8">
            <!-- Navigation Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-between">
                        @if($previousIncident)
                            <a href="{{ route('incidents.show', $previousIncident->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        @else
                            <div></div>
                        @endif
                        
                        @if($nextIncident)
                            <a href="{{ route('incidents.show', $nextIncident->id) }}" class="btn btn-sm btn-outline-secondary">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">{{ $incident->title }}</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Severity:</label>
                                <p>
                                    @if($incident->severity === 'Critical')
                                        <span class="badge badge-danger badge-lg">{{ $incident->severity }}</span>
                                    @elseif($incident->severity === 'High')
                                        <span class="badge badge-warning badge-lg">{{ $incident->severity }}</span>
                                    @elseif($incident->severity === 'Medium')
                                        <span class="badge badge-info badge-lg">{{ $incident->severity }}</span>
                                    @else
                                        <span class="badge badge-success badge-lg">{{ $incident->severity }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status:</label>
                                <p>
                                    @if($incident->status === 'Open')
                                        <span class="badge badge-primary badge-lg">{{ $incident->status }}</span>
                                    @elseif($incident->status === 'On Progress')
                                        <span class="badge badge-warning badge-lg">{{ $incident->status }}</span>
                                    @else
                                        <span class="badge badge-success badge-lg">{{ $incident->status }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reported By:</label>
                                <p>
                                    <strong>{{ $incident->reportedBy->name }}</strong><br>
                                    <small class="text-muted">{{ $incident->reportedBy->email }}</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Incident Date:</label>
                                <p>{{ $incident->incident_date->format('l, F d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description:</label>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($incident->description)) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Created At:</label>
                                <p>{{ $incident->created_at->format('M d, Y H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Updated At:</label>
                                <p>{{ $incident->updated_at->format('M d, Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Trail -->
            <div class="card card-info mt-3">
                <div class="card-header with-border">
                    <h3 class="card-title">Audit Trail</h3>
                </div>
                <div class="card-body">
                    @if($auditLogs->count() > 0)
                        <div class="timeline">
                            @foreach($auditLogs as $log)
                            @php
                                $logCreatedAt = $log->created_at;
                            @endphp
                            <div class="time-label">
                                <span class="bg-primary">{{ $logCreatedAt ? $logCreatedAt->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div>
                                <i class="fas fa-{{ $log->action === 'create' ? 'plus' : ($log->action === 'update' ? 'pencil-alt' : ($log->action === 'delete' ? 'trash' : 'eye')) }} bg-primary"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $logCreatedAt ? $logCreatedAt->format('H:i:s') : 'N/A' }}
                                    </span>
                                    <h3 class="timeline-header">
                                        <a href="#">{{ $log->user->name ?? 'System' }}</a> {{ ucfirst($log->action) }}d this incident
                                    </h3>
                                    <div class="timeline-body">
                                        @if($log->action === 'update' && $log->new_values)
                                        <strong>Changes:</strong>
                                        <ul class="list-unstyled">
                                            @foreach($log->new_values as $field => $newValue)
                                                @if(isset($log->old_values[$field]) && $log->old_values[$field] !== $newValue)
                                                <li>
                                                    <span class="badge badge-info">{{ ucfirst($field) }}</span>
                                                    <strong>{{ $log->old_values[$field] }}</strong> → <strong>{{ $newValue }}</strong>
                                                </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                        @endif
                                    </div>
                                    <div class="timeline-footer">
                                        <small class="text-muted">
                                            <i class="fas fa-globe"></i> IP: {{ $log->ip_address ?? 'N/A' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No audit logs available.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-md-4">
            <!-- Quick Info -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Quick Info</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">ID:</dt>
                        <dd class="col-sm-7">
                            <code>#{{ $incident->id }}</code>
                        </dd>

                        <dt class="col-sm-5">Priority:</dt>
                        <dd class="col-sm-7">
                            <strong>{{ \App\Models\Incident::SEVERITY_LEVELS[$incident->severity] }}/4</strong>
                        </dd>

                        <dt class="col-sm-5">Age:</dt>
                        <dd class="col-sm-7">
                            {{ $incident->created_at->diffForHumans() }}
                        </dd>

                        <dt class="col-sm-5">Days Open:</dt>
                        <dd class="col-sm-7">
                            {{ $incident->created_at->diffInDays() }} days
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Status Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="timeline">
                                <div class="time-label">
                                    <span class="bg-warning">Lifecycle</span>
                                </div>
                                <div>
                                    <i class="fas fa-plus-circle bg-success"></i>
                                    <div class="timeline-item">
                                        <span class="time">{{ $incident->created_at->format('M d, Y H:i') }}</span>
                                        <h3 class="timeline-header">Created</h3>
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-pencil-alt bg-info"></i>
                                    <div class="timeline-item">
                                        <span class="time">{{ $incident->updated_at->format('M d, Y H:i') }}</span>
                                        <h3 class="timeline-header">Last Updated</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Actions -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('incidents.edit', $incident->id) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i> Edit Incident
                    </a>
                    @if($incident->deleted_at)
                    <form action="{{ route('incidents.restore', $incident->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block mb-2">
                            <i class="fas fa-undo"></i> Restore
                        </button>
                    </form>
                    @else
                    <form action="{{ route('incidents.destroy', $incident->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
    /* Navigation buttons - compact sizing */
    .btn-sm {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d !important;
        color: #6c757d !important;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
    }
    
    /* Gap utility for flex */
    .gap-2 {
        gap: 0.5rem;
    }
</style>
@endpush
