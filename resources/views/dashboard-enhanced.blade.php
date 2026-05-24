@extends('adminlte::page')

@section('title', 'Dashboard - GreenAlert Incident Monitoring System')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">
                <i class="fas fa-chart-line"></i> Dashboard Monitoring Incident
            </h1>
            <small class="text-muted">Real-time incident monitoring and analytics</small>
        </div>
        <div class="text-right">
            <small class="text-muted">Last updated: {{ now()->format('H:i:s') }}</small>
        </div>
    </div>
@stop

@section('content')
    <!-- Key Statistics Widgets Row -->
    <div class="row mb-4">
        <!-- Total Incidents Widget -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-info shadow">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $stats['total_incidents'] }}</h3>
                    <p class="text-muted">Total Incidents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list"></i>
                </div>
                <a href="{{ route('incidents.index') }}" class="small-box-footer">
                    <i class="fas fa-arrow-circle-right"></i> View All
                </a>
            </div>
        </div>

        <!-- Critical Incidents Widget (Red Alert) -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-danger shadow {{ $stats['critical_incidents'] > 0 ? 'border-left-5 border-danger' : '' }}">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $stats['critical_incidents'] }}</h3>
                    <p class="text-muted">
                        <i class="fas fa-fire"></i> Critical Incidents
                    </p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('incidents.index', ['severity' => 'Critical']) }}" class="small-box-footer">
                    <i class="fas fa-arrow-circle-right"></i> Investigate
                </a>
            </div>
        </div>

        <!-- Open Incidents Widget -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-primary shadow">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $stats['open_incidents'] }}</h3>
                    <p class="text-muted">Open Incidents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <a href="{{ route('incidents.index', ['status' => 'Open']) }}" class="small-box-footer">
                    <i class="fas fa-arrow-circle-right"></i> Manage
                </a>
            </div>
        </div>

        <!-- Resolved Incidents Widget -->
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-success shadow">
                <div class="inner">
                    <h3 class="font-weight-bold">{{ $stats['resolved_incidents'] }}</h3>
                    <p class="text-muted">Resolved Incidents</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('incidents.index', ['status' => 'Resolved']) }}" class="small-box-footer">
                    <i class="fas fa-arrow-circle-right"></i> View
                </a>
            </div>
        </div>
    </div>

    <!-- Critical Alert Banner -->
    @if($stats['critical_unresolved'] > 0)
    <div class="alert alert-danger alert-dismissible fade show border-left-4 border-danger" role="alert">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-exclamation-circle fa-2x"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">🚨 CRITICAL ALERT</h4>
                <p class="mb-0">
                    <strong>{{ $stats['critical_unresolved'] }}</strong> unresolved CRITICAL incident(s) require immediate attention!
                    <a href="{{ route('incidents.index', ['severity' => 'Critical']) }}" class="font-weight-bold">Take Action →</a>
                </p>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Severity Distribution Pie Chart -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-primary card-outline shadow">
                <div class="card-header with-border bg-light">
                    <h3 class="card-title">
                        <i class="fas fa-pie-chart"></i> Severity Distribution
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="severityChart" height="240"></canvas>
                </div>
                <div class="card-footer small text-muted">
                    <i class="fas fa-info-circle"></i> Breakdown by severity level
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-lg-4 col-md-6">
            <div class="card card-success card-outline shadow">
                <div class="card-header with-border bg-light">
                    <h3 class="card-title">
                        <i class="fas fa-chart-doughnut"></i> Status Distribution
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="240"></canvas>
                </div>
                <div class="card-footer small text-muted">
                    <i class="fas fa-info-circle"></i> Current status breakdown
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4 col-md-12">
            <div class="card card-info card-outline shadow">
                <div class="card-header with-border bg-light">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt"></i> Quick Stats
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center border-right pb-3">
                            <div class="description-block">
                                <h5 class="description-header text-primary">{{ $stats['in_progress_incidents'] }}</h5>
                                <span class="description-text">In Progress</span>
                            </div>
                        </div>
                        <div class="col-6 text-center pb-3">
                            <div class="description-block">
                                <h5 class="description-header text-success">{{ $stats['resolved_incidents'] }}</h5>
                                <span class="description-text">Resolved (Today)</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-center border-right">
                            <div class="description-block">
                                <h5 class="description-header text-warning">{{ $stats['urgent_incidents'] }}</h5>
                                <span class="description-text">High/Critical</span>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="description-block">
                                <h5 class="description-header text-info">{{ now()->format('H:i') }}</h5>
                                <span class="description-text">Current Time</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incidents Trend Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-warning card-outline shadow">
                <div class="card-header with-border bg-light">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Incidents Trend (Last 30 Days)
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="trendChart" height="100"></canvas>
                </div>
                <div class="card-footer small text-muted">
                    <i class="fas fa-info-circle"></i> Severity trend analysis over time
                </div>
            </div>
        </div>
    </div>

    <!-- Recent & Critical Incidents Row -->
    <div class="row">
        <!-- Recent Incidents Table -->
        <div class="col-lg-8">
            <div class="card card-primary shadow">
                <div class="card-header with-border bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Recent Incidents
                    </h3>
                    <a href="{{ route('incidents.index') }}" class="btn btn-xs btn-primary">
                        <i class="fas fa-list"></i> View All
                    </a>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-hover table-striped mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 40%">Title</th>
                                <th style="width: 15%">Severity</th>
                                <th style="width: 15%">Status</th>
                                <th style="width: 20%">Date</th>
                                <th style="width: 10%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentIncidents as $incident)
                            <tr class="{{ $incident->severity === 'Critical' ? 'bg-danger bg-opacity-10 border-left-4 border-danger' : '' }}">
                                <td>
                                    <a href="{{ route('incidents.show', $incident->id) }}" class="font-weight-bold text-dark">
                                        {{ Str::limit($incident->title, 35) }}
                                    </a>
                                </td>
                                <td>
                                    @if($incident->severity === 'Critical')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-fire"></i> {{ $incident->severity }}
                                        </span>
                                    @elseif($incident->severity === 'High')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-exclamation"></i> {{ $incident->severity }}
                                        </span>
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
                                <td>
                                    <small class="text-muted">{{ $incident->incident_date->format('M d, Y') }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-xs btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <p>No recent incidents</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Showing {{ $recentIncidents->firstItem() ?? 0 }} to {{ $recentIncidents->lastItem() ?? 0 }} of {{ $recentIncidents->total() ?? 0 }} incidents
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

        <!-- Critical Incidents Priority List -->
        <div class="col-lg-4">
            <div class="card card-danger shadow">
                <div class="card-header with-border bg-danger text-white">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Critical Incidents
                    </h3>
                </div>
                <div class="card-body p-0">
                    @forelse($criticalIncidents as $incident)
                    <div class="list-group list-group-flush">
                        <a href="{{ route('incidents.show', $incident->id) }}" class="list-group-item list-group-item-action border-left-4 border-danger px-3 py-2 hover-light">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-dark font-weight-bold">
                                        <i class="fas fa-fire text-danger"></i>
                                        {{ Str::limit($incident->title, 25) }}
                                    </h6>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar"></i> {{ $incident->incident_date->format('M d, Y') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user"></i> {{ $incident->reportedBy->name ?? 'N/A' }}
                                    </small>
                                </div>
                                <span class="badge badge-danger">
                                    {{ $incident->severity }}
                                </span>
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <p class="mb-0">No critical incidents</p>
                        <small>All systems operational</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Urgent Incidents Alert Section -->
    @if(count($urgentIncidents) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card card-warning card-outline shadow">
                <div class="card-header with-border bg-light">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation"></i> Incidents Requiring Attention
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($urgentIncidents as $incident)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-{{ $incident->severity === 'Critical' ? 'danger' : 'warning' }} h-100">
                                <div class="card-body pb-2">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0 font-weight-bold">
                                            {{ Str::limit($incident->title, 40) }}
                                        </h6>
                                        <span class="badge badge-{{ $incident->severity === 'Critical' ? 'danger' : 'warning' }}">
                                            {{ $incident->severity }}
                                        </span>
                                    </div>
                                    <p class="card-text small text-muted mb-2">
                                        <i class="fas fa-calendar"></i> {{ $incident->incident_date->format('M d, Y') }}
                                        | <i class="fas fa-user"></i> {{ $incident->reportedBy->name ?? 'N/A' }}
                                    </p>
                                    <a href="{{ route('incidents.show', $incident->id) }}" class="btn btn-sm btn-outline-{{ $incident->severity === 'Critical' ? 'danger' : 'warning' }}">
                                        <i class="fas fa-eye"></i> View Details
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
    // Chart color scheme
    const severityColors = {
        'Critical': 'rgba(220, 53, 69, 0.8)',  // Red
        'High': 'rgba(255, 193, 7, 0.8)',     // Orange/Yellow
        'Medium': 'rgba(23, 162, 184, 0.8)',  // Cyan
        'Low': 'rgba(40, 167, 69, 0.8)'       // Green
    };

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
                    position: 'bottom',
                    labels: {
                        font: { size: 12 },
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Status Distribution Chart
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
                    'rgba(0, 123, 255, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 167, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(0, 123, 255, 1)',
                    'rgba(255, 193, 7, 1)',
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
                    position: 'bottom',
                    labels: {
                        font: { size: 12 },
                        padding: 15,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Severity Trend Line Chart (Last 30 Days)
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
                    position: 'top',
                    labels: {
                        font: { size: 12 },
                        padding: 15,
                        usePointStyle: true
                    }
                },
                filler: true
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
    /* Dashboard enhancements */
    .small-box {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .small-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
    }

    .card {
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.03);
    }

    .list-group-item.hover-light:hover {
        background-color: rgba(0,0,0,0.05);
    }

    .border-left-4 {
        border-left: 4px solid;
    }

    .border-danger {
        border-color: #dc3545 !important;
    }

    .bg-opacity-10 {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }

    .card-header.bg-light {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6;
    }

    /* Pagination styling */
    .btn-outline-secondary {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
    }

    .btn-outline-secondary.disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .small-box .inner h3 {
            font-size: 1.5rem !important;
        }

        .card-body table {
            font-size: 0.85rem;
        }
    }

    /* Critical alert enhancement */
    .alert {
        border-radius: 0.5rem;
    }

    /* Icon colors */
    .text-danger { color: #dc3545; }
    .text-success { color: #28a745; }
    .text-warning { color: #ffc107; }
    .text-info { color: #17a2b8; }
</style>
@endpush
