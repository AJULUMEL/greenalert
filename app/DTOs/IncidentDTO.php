<?php

namespace App\DTOs;

use Carbon\Carbon;

/**
 * Incident Data Transfer Object
 * 
 * Immutable data holder for incident information.
 * Ensures type safety and data consistency.
 */
class IncidentDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $severity,
        public readonly string $status,
        public readonly Carbon $incident_date,
        public readonly int $reported_by,
        public readonly ?int $id = null,
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'],
            severity: $data['severity'],
            status: $data['status'],
            incident_date: Carbon::parse($data['incident_date']),
            reported_by: $data['reported_by'],
            id: $data['id'] ?? null,
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'severity' => $this->severity,
            'status' => $this->status,
            'incident_date' => $this->incident_date->format('Y-m-d'),
            'reported_by' => $this->reported_by,
        ];
    }

    /**
     * Get only fillable attributes
     */
    public function toFillableArray(): array
    {
        $data = $this->toArray();
        unset($data['id']);
        return $data;
    }
}
