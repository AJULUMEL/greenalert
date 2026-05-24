<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'severity',
        'status',
        'reported_by',
        'incident_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'incident_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Severity levels with priority
     */
    public const SEVERITY_LEVELS = [
        'Low' => 1,
        'Medium' => 2,
        'High' => 3,
        'Critical' => 4,
    ];

    /**
     * Status types
     */
    public const STATUS_TYPES = [
        'Open' => 'Open',
        'On Progress' => 'On Progress',
        'Resolved' => 'Resolved',
    ];

    /**
     * Get the user who reported the incident
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the audit logs for this incident
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if incident is critical
     */
    public function isCritical(): bool
    {
        return $this->severity === 'Critical';
    }

    /**
     * Check if incident is urgent (High or Critical)
     */
    public function isUrgent(): bool
    {
        return in_array($this->severity, ['High', 'Critical']);
    }

    /**
     * Get severity priority level
     */
    public function getSeverityPriority(): int
    {
        return self::SEVERITY_LEVELS[$this->severity] ?? 0;
    }

    /**
     * Scope: Filter by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get critical incidents
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'Critical');
    }

    /**
     * Scope: Get urgent incidents (High + Critical)
     */
    public function scopeUrgent($query)
    {
        return $query->whereIn('severity', ['High', 'Critical']);
    }

    /**
     * Scope: Get open incidents
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    /**
     * Scope: Order by severity (descending priority)
     */
    public function scopeOrderBySeverity($query, $direction = 'desc')
    {
        return $query->orderByRaw("FIELD(severity, 'Critical', 'High', 'Medium', 'Low') {$direction}");
    }

    /**
     * Scope: Order by recent
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('incident_date', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Search by title or description
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->whereRaw("MATCH(title, description) AGAINST(? IN BOOLEAN MODE)", [$keyword]);
    }
}
