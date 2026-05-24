<?php

namespace App\Enums;

/**
 * Severity Level Enumeration
 * 
 * Defines incident severity levels with ordering.
 * Lower numeric values = less severe.
 */
enum SeverityLevel: string
{
    case LOW = 'Low';
    case MEDIUM = 'Medium';
    case HIGH = 'High';
    case CRITICAL = 'Critical';

    /**
     * Get severity priority (higher = more severe)
     */
    public function priority(): int
    {
        return match($this) {
            self::CRITICAL => 4,
            self::HIGH => 3,
            self::MEDIUM => 2,
            self::LOW => 1,
        };
    }

    /**
     * Get badge color for display
     */
    public function color(): string
    {
        return match($this) {
            self::CRITICAL => 'danger',
            self::HIGH => 'warning',
            self::MEDIUM => 'info',
            self::LOW => 'success',
        };
    }

    /**
     * Get hex color for charts
     */
    public function hexColor(): string
    {
        return match($this) {
            self::CRITICAL => 'rgba(220, 53, 69, 0.8)',      // Red
            self::HIGH => 'rgba(255, 193, 7, 0.8)',          // Orange
            self::MEDIUM => 'rgba(23, 162, 184, 0.8)',       // Cyan
            self::LOW => 'rgba(40, 167, 69, 0.8)',           // Green
        };
    }

    /**
     * Get all values for validation
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
