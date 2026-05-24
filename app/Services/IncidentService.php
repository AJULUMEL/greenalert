<?php

namespace App\Services;

use App\DTOs\IncidentDTO;
use App\DTOs\FilterDTO;
use App\Repositories\IncidentRepository;
use App\Models\Incident;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Incident Service
 * 
 * Handles business logic for incident management.
 * Coordinates between controllers and repositories.
 */
class IncidentService
{
    public function __construct(
        private IncidentRepository $repository,
    ) {}

    /**
     * Get paginated incidents with filters
     */
    public function getPaginated(FilterDTO $filter)
    {
        return $this->repository->getPaginated($filter);
    }

    /**
     * Get incident with relations
     */
    public function getWithRelations(int $id): Incident
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Create new incident
     */
    public function create(IncidentDTO $dto): Incident
    {
        return DB::transaction(function () use ($dto) {
            try {
                return $this->repository->create($dto->toFillableArray());
            } catch (Exception $e) {
                throw new Exception('Failed to create incident: ' . $e->getMessage());
            }
        });
    }

    /**
     * Update incident
     */
    public function update(Incident $incident, IncidentDTO $dto): bool
    {
        return DB::transaction(function () use ($incident, $dto) {
            try {
                return $this->repository->update($incident, $dto->toFillableArray());
            } catch (Exception $e) {
                throw new Exception('Failed to update incident: ' . $e->getMessage());
            }
        });
    }

    /**
     * Delete incident
     */
    public function delete(Incident $incident): bool
    {
        return DB::transaction(function () use ($incident) {
            try {
                return $this->repository->delete($incident);
            } catch (Exception $e) {
                throw new Exception('Failed to delete incident: ' . $e->getMessage());
            }
        });
    }

    /**
     * Restore incident
     */
    public function restore(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            try {
                return $this->repository->restore($id);
            } catch (Exception $e) {
                throw new Exception('Failed to restore incident: ' . $e->getMessage());
            }
        });
    }

    /**
     * Get incident navigation
     */
    public function getNavigation(int $currentId): array
    {
        return [
            'previous' => $this->repository->getPrevious($currentId),
            'next' => $this->repository->getNext($currentId),
        ];
    }

    /**
     * Get exportable incidents
     */
    public function getExportable(FilterDTO $filter)
    {
        return $this->repository->getPaginated($filter)->all();
    }

    /**
     * Get statistics
     */
    public function getStatistics(): array
    {
        return $this->repository->getStatistics();
    }

    /**
     * Get severity breakdown
     */
    public function getSeverityBreakdown(): array
    {
        $breakdown = $this->repository->getSeverityBreakdown();
        
        return [
            'Critical' => $breakdown['Critical'] ?? 0,
            'High' => $breakdown['High'] ?? 0,
            'Medium' => $breakdown['Medium'] ?? 0,
            'Low' => $breakdown['Low'] ?? 0,
        ];
    }

    /**
     * Get status breakdown
     */
    public function getStatusBreakdown(): array
    {
        $breakdown = $this->repository->getStatusBreakdown();
        
        return [
            'Open' => $breakdown['Open'] ?? 0,
            'On Progress' => $breakdown['On Progress'] ?? 0,
            'Resolved' => $breakdown['Resolved'] ?? 0,
        ];
    }

    /**
     * Get severity trend for charts
     */
    public function getSeverityTrend(): array
    {
        return $this->repository->getSeverityTrend();
    }

    /**
     * Format incident for export
     */
    public function formatForExport(Incident $incident): array
    {
        return [
            'id' => $incident->id,
            'title' => $incident->title,
            'severity' => $incident->severity,
            'status' => $incident->status,
            'reporter' => $incident->reportedBy->name,
            'date' => $incident->incident_date->format('Y-m-d'),
            'created' => $incident->created_at->format('Y-m-d H:i:s'),
            'updated' => $incident->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
