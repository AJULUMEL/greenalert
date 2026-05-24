<?php

namespace App\Enums;

/**
 * Incident Status Enumeration
 * 
 * Defines incident states through lifecycle.
 */
enum IncidentStatus: string
{
    case OPEN = 'Open';
    case IN_PROGRESS = 'On Progress';
    case RESOLVED = 'Resolved';

    /**
     * Get badge color for display
     */
    public function color(): string
    {
        return match($this) {
            self::OPEN => 'primary',
            self::IN_PROGRESS => 'warning',
            self::RESOLVED => 'success',
        };
    }

    /**
     * Get hex color for charts
     */
    public function hexColor(): string
    {
        return match($this) {
            self::OPEN => 'rgba(13, 110, 253, 0.8)',          // Blue
            self::IN_PROGRESS => 'rgba(255, 193, 7, 0.8)',    // Yellow
            self::RESOLVED => 'rgba(25, 135, 84, 0.8)',       // Green
        };
    }

    /**
     * Check if status is terminal
     */
    public function isTerminal(): bool
    {
        return $this === self::RESOLVED;
    }

    /**
     * Get all values for validation
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
