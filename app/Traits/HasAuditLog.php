<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Http\Request;

/**
 * Audit Logging Trait
 * 
 * Provides reusable audit trail functionality.
 * Automatically logs user actions with context.
 */
trait HasAuditLog
{
    /**
     * Log audit entry for creation
     */
    protected function logCreated($model, Request $request): void
    {
        $this->createAuditLog(
            action: 'create',
            model: $model,
            request: $request,
            newValues: $model->toArray(),
        );
    }

    /**
     * Log audit entry for update
     */
    protected function logUpdated($model, array $oldValues, Request $request): void
    {
        $this->createAuditLog(
            action: 'update',
            model: $model,
            request: $request,
            oldValues: $oldValues,
            newValues: $model->fresh()->toArray(),
        );
    }

    /**
     * Log audit entry for deletion
     */
    protected function logDeleted($model, Request $request): void
    {
        $this->createAuditLog(
            action: 'delete',
            model: $model,
            request: $request,
            oldValues: $model->toArray(),
        );
    }

    /**
     * Log audit entry for viewing
     */
    protected function logViewed($model, Request $request): void
    {
        $this->createAuditLog(
            action: 'view',
            model: $model,
            request: $request,
        );
    }

    /**
     * Log audit entry for restoration
     */
    protected function logRestored($model, Request $request): void
    {
        $this->createAuditLog(
            action: 'restore',
            model: $model,
            request: $request,
        );
    }

    /**
     * Create audit log entry
     */
    protected function createAuditLog(
        string $action,
        $model,
        Request $request,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        AuditLog::create([
            'user_id' => auth()->id(),
            'incident_id' => $model->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
