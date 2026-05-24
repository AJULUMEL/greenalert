@extends('adminlte::page')

@section('title', 'Incidents List - GreenAlert')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Incidents Management</h1>
        <a href="{{ route('incidents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Incident
        </a>
    </div>
@stop

@section('content')
    <!-- Filter & Search Card -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Filter & Search</h3>
                </div>
                <form method="GET" action="{{ route('incidents.index') }}" class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Severity</label>
                                <select name="severity" class="form-control">
                                    <option value="">-- All Severities --</option>
                                    <option value="Low" {{ request('severity') === 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Medium" {{ request('severity') === 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="High" {{ request('severity') === 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Critical" {{ request('severity') === 'Critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">-- All Status --</option>
                                    <option value="Open" {{ request('status') === 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="On Progress" {{ request('status') === 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                    <option value="Resolved" {{ request('status') === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Title or Description..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('incidents.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                            <a href="{{ route('incidents.export', request()->query()) }}" class="btn btn-success float-right">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Incidents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['critical'] }}</h3>
                    <p>Critical</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fire"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['open'] }}</h3>
                    <p>Open</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['in_progress'] }}</h3>
                    <p>In Progress</p>
                </div>
                <div class="icon">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Incidents Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Incidents List</h3>
                </div>
                <div class="card-body table-responsive">
                    @if($incidents->count() > 0)
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 25%">Title</th>
                                    <th style="width: 12%">Severity</th>
                                    <th style="width: 12%">Status</th>
                                    <th style="width: 18%">Reported By</th>
                                    <th style="width: 15%">Incident Date</th>
                                    <th style="width: 13%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incidents as $incident)
                                <tr>
                                    <td>
                                        <a href="{{ route('incidents.show', $incident->id) }}">
                                            #{{ $incident->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('incidents.show', $incident->id) }}">
                                            {{ Str::limit($incident->title, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($incident->severity === 'Critical')
                                            <span class="badge badge-danger">{{ $incident->severity }}</span>
                                        @elseif($incident->severity === 'High')
                                            <span class="badge badge-warning">{{ $incident->severity }}</span>
                                        @elseif($incident->severity === 'Medium')
                                            <span class="badge badge-info">{{ $incident->severity }}</span>
                                        @else
                                            <span class="badge badge-success">{{ $incident->severity }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($incident->status === 'Open')
                                            <span class="badge badge-primary">{{ $incident->status }}</span>
                                        @elseif($incident->status === 'On Progress')
                                            <span class="badge badge-warning">{{ $incident->status }}</span>
                                        @else
                                            <span class="badge badge-success">{{ $incident->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $incident->reportedBy->name }}</td>
                                    <td>{{ $incident->incident_date->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-xs btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('incidents.edit', $incident->id) }}" class="btn btn-xs btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('incidents.destroy', $incident->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Simple Pagination -->
                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <small class="text-muted">
                                Showing {{ $incidents->firstItem() }} to {{ $incidents->lastItem() }} of {{ $incidents->total() }} incidents
                            </small>
                            <div>
                                @if($incidents->onFirstPage())
                                    <span class="btn btn-sm btn-outline-secondary disabled">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </span>
                                @else
                                    <a href="{{ $incidents->previousPageUrl() }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                @endif
                                
                                <span class="mx-2 text-muted">
                                    Page {{ $incidents->currentPage() }} of {{ $incidents->lastPage() }}
                                </span>
                                
                                @if($incidents->hasMorePages())
                                    <a href="{{ $incidents->nextPageUrl() }}" class="btn btn-sm btn-outline-secondary">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                @else
                                    <span class="btn btn-sm btn-outline-secondary disabled">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No incidents found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

@push('css')
<style>
    /* Clean pagination styling */
    .btn-outline-secondary {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
    }
    
    .btn-outline-secondary.disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
</style>
@endpush
