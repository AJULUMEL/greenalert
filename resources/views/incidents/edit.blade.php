@extends('adminlte::page')

@section('title', 'Edit Incident - GreenAlert')

@section('content_header')
    <h1>Edit Incident #{{ $incident->id }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Incident Information</h3>
                </div>

                <form method="POST" action="{{ route('incidents.update', $incident->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <!-- Title -->
                        <div class="form-group">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $incident->title) }}" placeholder="Enter incident title" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="6" placeholder="Describe the incident in detail..." required>{{ old('description', $incident->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Provide detailed information about the incident</small>
                        </div>

                        <div class="row">
                            <!-- Severity -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="severity">Severity <span class="text-danger">*</span></label>
                                    <select name="severity" id="severity" class="form-control @error('severity') is-invalid @enderror" required>
                                        <option value="">-- Select Severity --</option>
                                        <option value="Low" {{ old('severity', $incident->severity) === 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Medium" {{ old('severity', $incident->severity) === 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="High" {{ old('severity', $incident->severity) === 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Critical" {{ old('severity', $incident->severity) === 'Critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('severity')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="Open" {{ old('status', $incident->status) === 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="On Progress" {{ old('status', $incident->status) === 'On Progress' ? 'selected' : '' }}>On Progress</option>
                                        <option value="Resolved" {{ old('status', $incident->status) === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Incident Date -->
                        <div class="form-group">
                            <label for="incident_date">Incident Date <span class="text-danger">*</span></label>
                            <input type="date" name="incident_date" id="incident_date" class="form-control @error('incident_date') is-invalid @enderror" 
                                   value="{{ old('incident_date', $incident->incident_date->format('Y-m-d')) }}" required>
                            @error('incident_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Incident
                        </button>
                        <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Incident Info -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Incident Details</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">ID:</dt>
                        <dd class="col-sm-6">#{{ $incident->id }}</dd>

                        <dt class="col-sm-6">Reported By:</dt>
                        <dd class="col-sm-6">{{ $incident->reportedBy->name }}</dd>

                        <dt class="col-sm-6">Created:</dt>
                        <dd class="col-sm-6">{{ $incident->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-6">Updated:</dt>
                        <dd class="col-sm-6">{{ $incident->updated_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Severity Guide -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Severity Guide</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge badge-success">Low</span>
                        <p class="small mt-1">Minor issues that don't affect system operations</p>
                    </div>
                    <div class="mb-3">
                        <span class="badge badge-info">Medium</span>
                        <p class="small mt-1">Moderate issues affecting some features</p>
                    </div>
                    <div class="mb-3">
                        <span class="badge badge-warning">High</span>
                        <p class="small mt-1">Significant issues requiring urgent attention</p>
                    </div>
                    <div>
                        <span class="badge badge-danger">Critical</span>
                        <p class="small mt-1">Critical issues affecting core operations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
