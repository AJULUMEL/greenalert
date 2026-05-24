<?php

namespace App\Repositories;

use App\Models\Incident;
use App\DTOs\FilterDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Incident Repository
 * 
 * Handles all data access operations for incidents.
 * Abstracts database layer from business logic.
 */
class IncidentRepository
{
    /**
     * Get paginated incidents with filters
     */
    public function getPaginated(FilterDTO $filter): LengthAwarePaginator
    {
        return $this->applyFilters($filter)
            ->orderByRaw("FIELD(severity, 'Critical', 'High', 'Medium', 'Low')")
            ->orderBy('incident_date', 'desc')
            ->paginate($filter->perPage)
            ->appends($filter->getQueryParams());
    }

    /**
     * Get recent incidents
     */
    public function getRecent(int $limit = 10): LengthAwarePaginator
    {
        return Incident::query()
            ->orderByRaw("FIELD(severity, 'Critical', 'High', 'Medium', 'Low')")
            ->orderBy('incident_date', 'desc')
            ->paginate($limit);
    }

    /**
     * Get critical incidents
     */
    public function getCritical(int $limit = 5): Collection
    {
        return Incident::query()
            ->where('severity', 'Critical')
            ->orderBy('incident_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get urgent incidents
     */
    public function getUrgent(int $limit = 10): Collection
    {
        return Incident::query()
            ->whereIn('severity', ['High', 'Critical'])
            ->where('status', '!=', 'Resolved')
            ->orderByRaw("FIELD(severity, 'Critical', 'High')")
            ->limit($limit)
            ->get();
    }

    /**
     * Get incident by ID with relations
     */
    public function findWithRelations(int $id): ?Incident
    {
        return Incident::with('reportedBy', 'auditLogs.user')
            ->findOrFail($id);
    }

    /**
     * Get previous incident
     */
    public function getPrevious(int $currentId): ?Incident
    {
        return Incident::query()
            ->where('id', '<', $currentId)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Get next incident
     */
    public function getNext(int $currentId): ?Incident
    {
        return Incident::query()
            ->where('id', '>', $currentId)
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Create incident
     */
    public function create(array $data): Incident
    {
        return Incident::create($data);
    }

    /**
     * Update incident
     */
    public function update(Incident $incident, array $data): bool
    {
        return $incident->update($data);
    }

    /**
     * Soft delete incident
     */
    public function delete(Incident $incident): bool
    {
        return $incident->delete();
    }

    /**
     * Restore incident
     */
    public function restore(int $id): bool
    {
        $incident = Incident::withTrashed()->findOrFail($id);
        return $incident->restore();
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        $total = Incident::count();
        $critical = Incident::where('severity', 'Critical')->count();
        $urgent = Incident::whereIn('severity', ['High', 'Critical'])->count();
        $open = Incident::where('status', 'Open')->count();
        $inProgress = Incident::where('status', 'On Progress')->count();
        $resolved = Incident::where('status', 'Resolved')->count();
        $criticalUnresolved = Incident::where('severity', 'Critical')
            ->whereIn('status', ['Open', 'On Progress'])
            ->count();

        return [
            'total' => $total,
            'total_incidents' => $total,
            'critical' => $critical,
            'critical_incidents' => $critical,
            'urgent' => $urgent,
            'urgent_incidents' => $urgent,
            'open' => $open,
            'open_incidents' => $open,
            'in_progress' => $inProgress,
            'in_progress_incidents' => $inProgress,
            'resolved' => $resolved,
            'resolved_incidents' => $resolved,
            'critical_unresolved' => $criticalUnresolved,
        ];
    }

    /**
     * Get severity breakdown
     */
    public function getSeverityBreakdown(): array
    {
        return Incident::query()
            ->selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->pluck('count', 'severity')
            ->toArray();
    }

    /**
     * Get status breakdown
     */
    public function getStatusBreakdown(): array
    {
        return Incident::query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Get severity trend (last 30 days)
     */
    public function getSeverityTrend(): array
    {
        $data = Incident::query()
            ->selectRaw('DATE(created_at) as date, severity, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date', 'severity')
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('date');

        $labels = [];
        $datasets = [
            'Critical' => [],
            'High' => [],
            'Medium' => [],
            'Low' => [],
        ];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('M d');
            $labels[] = $date;

            $dateStr = now()->subDays($i)->format('Y-m-d');
            foreach (['Critical', 'High', 'Medium', 'Low'] as $severity) {
                $datasets[$severity][] = $data->get($dateStr)
                    ?->firstWhere('severity', $severity)
                    ?->count ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => array_map(fn($severity, $data) => [
                'label' => $severity,
                'data' => $data,
            ], array_keys($datasets), $datasets),
        ];
    }

    /**
     * Apply filters to query
     */
    private function applyFilters(FilterDTO $filter)
    {
        $query = Incident::query();

        if ($filter->severity) {
            $query->where('severity', $filter->severity);
        }

        if ($filter->status) {
            $query->where('status', $filter->status);
        }

        if ($filter->search) {
            $keyword = $filter->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }
}
