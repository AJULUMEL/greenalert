@extends('adminlte::page')

@section('title', 'Dashboard - GreenAlert')

@section('content_header')
    <h1>Dashboard Monitoring Incident</h1>
@stop

@section('content')
    <!-- Key Statistics Row -->
    <div class="row">
        <!-- Total Incidents -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_incidents'] }}</h3>
                    <p>Total Incidents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('incidents.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Critical Incidents -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['critical_incidents'] }}</h3>
                    <p>Critical Incidents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-fire"></i>
                </div>
                <a href="{{ route('incidents.index', ['severity' => 'Critical']) }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Urgent Incidents -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['urgent_incidents'] }}</h3>
                    <p>Urgent Incidents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation"></i>
                </div>
                <a href="{{ route('incidents.index', ['severity' => 'High']) }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Open Incidents -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['open_incidents'] }}</h3>
                    <p>Open Incidents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <a href="{{ route('incidents.index', ['status' => 'Open']) }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Critical Incidents Alert -->
    @if($stats['critical_unresolved'] > 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">⚠️ PERHATIAN!</h4>
        <p>Terdapat <strong>{{ $stats['critical_unresolved'] }}</strong> incident dengan severity CRITICAL yang belum diselesaikan!</p>
        <hr>
        <p class="mb-0">Silakan segera periksa dan tangani incident tersebut.</p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <!-- Severity Distribution Pie Chart -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Distribusi Severity</h3>
                </div>
                <div class="card-body">
                    <canvas id="severityChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Status Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Status Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center border-right">
                            <div class="description-block border-bottom pb-3">
                                <h5 class="description-header">{{ $stats['in_progress_incidents'] }}</h5>
                                <span class="description-text">In Progress</span>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="description-block">
                                <h5 class="description-header">{{ $stats['resolved_incidents'] }}</h5>
                                <span class="description-text">Resolved</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Severity Trend Chart -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">Incident Trend (Last 30 Days)</h3>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Critical Incidents List -->
        <div class="col-lg-4">
            <div class="card card-danger">
                <div class="card-header with-border">
                    <h3 class="card-title">🔴 Critical Incidents</h3>
                </div>
                <div class="card-body p-0">
                    @forelse($criticalIncidents as $incident)
                    <div class="list-group list-group-flush">
                        <a href="{{ route('incidents.show', $incident->id) }}" class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1 text-danger">
                                        <strong>{{ Str::limit($incident->title, 25) }}</strong>
                                    </h6>
                                    <small class="text-muted">{{ $incident->incident_date->format('M d, Y') }}</small>
                                </div>
                                <span class="badge badge-danger">{{ $incident->severity }}</span>
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>Tidak ada critical incident</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Incidents Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header with-border d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Recent Incidents</h3>
                    <a href="{{ route('incidents.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> View All
                    </a>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Severity</th>
                                <th>Status</th>
                                <th>Reported By</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentIncidents as $incident)
                            <tr>
                                <td>
                                    <a href="{{ route('incidents.show', $incident->id) }}">
                                        {{ Str::limit($incident->title, 40) }}
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
                                    <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-xs btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No incidents found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- Simple Pagination -->
                    <div class="d-flex justify-content-between align-items-center pt-3">
                        <small class="text-muted">
                            Showing {{ $recentIncidents->firstItem() }} to {{ $recentIncidents->lastItem() }} of {{ $recentIncidents->total() }} incidents
                        </small>
                        <div>
                            @if($recentIncidents->onFirstPage())
                                <span class="btn btn-sm btn-outline-secondary disabled">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </span>
                            @else
                                <a href="{{ $recentIncidents->previousPageUrl() }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            @endif
                            
                            <span class="mx-2 text-muted">
                                Page {{ $recentIncidents->currentPage() }} of {{ $recentIncidents->lastPage() }}
                            </span>
                            
                            @if($recentIncidents->hasMorePages())
                                <a href="{{ $recentIncidents->nextPageUrl() }}" class="btn btn-sm btn-outline-secondary">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="btn btn-sm btn-outline-secondary disabled">
                                    Next <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Urgent Incidents Alert -->
    @if(count($urgentIncidents) > 0)
    <div class="row mt-3">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header with-border">
                    <h3 class="card-title">⚠️ Urgent Incidents (Requiring Attention)</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($urgentIncidents as $incident)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-danger h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <span class="badge badge-{{ $incident->severity === 'Critical' ? 'danger' : 'warning' }}">
                                            {{ $incident->severity }}
                                        </span>
                                        {{ Str::limit($incident->title, 50) }}
                                    </h6>
                                    <p class="card-text small">
                                        <i class="fas fa-calendar"></i> {{ $incident->incident_date->format('M d, Y') }}
                                        | <i class="fas fa-user"></i> {{ $incident->reportedBy->name }}
                                    </p>
                                    <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Severity Distribution Pie Chart
    const severityCtx = document.getElementById('severityChart').getContext('2d');
    new Chart(severityCtx, {
        type: 'doughnut',
        data: {
            labels: ['Critical', 'High', 'Medium', 'Low'],
            datasets: [{
                data: [
                    {{ $severityBreakdown['Critical'] ?? 0 }},
                    {{ $severityBreakdown['High'] ?? 0 }},
                    {{ $severityBreakdown['Medium'] ?? 0 }},
                    {{ $severityBreakdown['Low'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(40, 167, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(220, 53, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(40, 167, 69, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Status Distribution Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Open', 'On Progress', 'Resolved'],
            datasets: [{
                data: [
                    {{ $statusBreakdown['Open'] ?? 0 }},
                    {{ $statusBreakdown['On Progress'] ?? 0 }},
                    {{ $statusBreakdown['Resolved'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(13, 110, 253, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(25, 135, 84, 0.8)'
                ],
                borderColor: [
                    'rgba(13, 110, 253, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(25, 135, 84, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($severityTrend['labels']) !!},
            datasets: {!! json_encode($severityTrend['datasets']) !!}
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush

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
