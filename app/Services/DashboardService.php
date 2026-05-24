<?php

namespace App\Services;

use App\Repositories\IncidentRepository;

/**
 * Dashboard Service
 * 
 * Handles dashboard data aggregation and preparation.
 */
class DashboardService
{
    public function __construct(
        private IncidentRepository $repository,
    ) {}

    /**
     * Get all dashboard data
     */
    public function getDashboardData(): array
    {
        $stats = $this->repository->getStatistics();
        $severityBreakdown = $this->formatSeverityBreakdown();
        $statusBreakdown = $this->formatStatusBreakdown();
        $recentIncidents = $this->repository->getRecent(10);
        $criticalIncidents = $this->repository->getCritical(5);
        $urgentIncidents = $this->repository->getUrgent(10);
        $severityTrend = $this->repository->getSeverityTrend();

        return [
            'stats' => $stats,
            'severityBreakdown' => $severityBreakdown,
            'statusBreakdown' => $statusBreakdown,
            'recentIncidents' => $recentIncidents,
            'criticalIncidents' => $criticalIncidents,
            'urgentIncidents' => $urgentIncidents,
            'severityTrend' => $severityTrend,
            'severity_breakdown' => $severityBreakdown,
            'status_breakdown' => $statusBreakdown,
            'recent_incidents' => $recentIncidents,
            'critical_incidents' => $criticalIncidents,
            'urgent_incidents' => $urgentIncidents,
            'severity_trend' => $severityTrend,
        ];
    }

    /**
     * Format severity breakdown for charts
     */
    private function formatSeverityBreakdown(): array
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
     * Format status breakdown for charts
     */
    private function formatStatusBreakdown(): array
    {
        $breakdown = $this->repository->getStatusBreakdown();
        
        return [
            'Open' => $breakdown['Open'] ?? 0,
            'On Progress' => $breakdown['On Progress'] ?? 0,
            'Resolved' => $breakdown['Resolved'] ?? 0,
        ];
    }

    /**
     * Get dashboard alerts
     */
    public function getAlerts(): array
    {
        $stats = $this->repository->getStatistics();
        
        return [
            'has_critical' => $stats['critical_unresolved'] > 0,
            'critical_count' => $stats['critical_unresolved'],
        ];
    }
}
