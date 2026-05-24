<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Audit Service
 * 
 * Handles audit log operations and compliance tracking.
 */
class AuditService
{
    /**
     * Create audit log entry
     */
    public function log(array $data): AuditLog
    {
        return AuditLog::create($data);
    }

    /**
     * Get audit logs for incident
     */
    public function getForIncident(int $incidentId, int $limit = 50): LengthAwarePaginator
    {
        return AuditLog::query()
            ->where('incident_id', $incidentId)
            ->with('user')
            ->latest()
            ->paginate($limit);
    }

    /**
     * Get user's recent actions
     */
    public function getUserActions(int $userId, int $limit = 20)
    {
        return AuditLog::query()
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent actions
     */
    public function getRecent(int $limit = 50)
    {
        return AuditLog::query()
            ->with('user', 'incident')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get action count by type
     */
    public function getActionStats(int $days = 30): array
    {
        return AuditLog::query()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();
    }

    /**
     * Purge old audit logs
     */
    public function purgeOld(int $days = 365): int
    {
        return AuditLog::query()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Get change history for incident
     */
    public function getChangeHistory(int $incidentId): array
    {
        $logs = AuditLog::query()
            ->where('incident_id', $incidentId)
            ->where('action', 'update')
            ->latest()
            ->get();

        return $logs->map(fn($log) => [
            'date' => $log->created_at,
            'user' => $log->user?->name,
            'action' => $log->action,
            'changes' => $this->formatChanges($log->old_values, $log->new_values),
        ])->toArray();
    }

    /**
     * Format changes for display
     */
    private function formatChanges(?array $old, ?array $new): array
    {
        if (!$old || !$new) {
            return [];
        }

        $changes = [];
        foreach ($new as $key => $value) {
            if (isset($old[$key]) && $old[$key] !== $value) {
                $changes[$key] = [
                    'from' => $old[$key],
                    'to' => $value,
                ];
            }
        }

        return $changes;
    }
}
