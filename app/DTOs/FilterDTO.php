<?php

namespace App\DTOs;

/**
 * Filter Data Transfer Object
 * 
 * Encapsulates filtering parameters for queries.
 */
class FilterDTO
{
    public function __construct(
        public readonly ?string $severity = null,
        public readonly ?string $status = null,
        public readonly ?string $search = null,
        public readonly int $perPage = 10,
        public readonly string $sortBy = 'severity',
        public readonly string $sortDirection = 'asc',
    ) {}

    /**
     * Create from request
     */
    public static function fromRequest($request): self
    {
        return new self(
            severity: $request->filled('severity') ? $request->severity : null,
            status: $request->filled('status') ? $request->status : null,
            search: $request->filled('search') ? $request->search : null,
            perPage: (int) ($request->input('per_page', 10)),
            sortBy: $request->input('sort_by', 'severity'),
            sortDirection: $request->input('sort_direction', 'asc'),
        );
    }

    /**
     * Get query parameters for pagination
     */
    public function getQueryParams(): array
    {
        $params = [];
        
        if ($this->severity) {
            $params['severity'] = $this->severity;
        }
        if ($this->status) {
            $params['status'] = $this->status;
        }
        if ($this->search) {
            $params['search'] = $this->search;
        }
        
        return $params;
    }

    /**
     * Check if any filter is active
     */
    public function hasFilters(): bool
    {
        return $this->severity !== null 
            || $this->status !== null 
            || $this->search !== null;
    }
}
