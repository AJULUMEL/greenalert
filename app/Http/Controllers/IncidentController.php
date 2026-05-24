<?php

namespace App\Http\Controllers;

use App\DTOs\IncidentDTO;
use App\DTOs\FilterDTO;
use App\Models\Incident;
use App\Services\IncidentService;
use App\Services\AuditService;
use App\Traits\HasAuditLog;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class IncidentController extends Controller
{
    use HasAuditLog;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        private IncidentService $service,
        private AuditService $auditService,
    ) {
        $this->middleware('auth');
    }

    /**
     * Display a listing of incidents with filters and search
     */
    public function index(Request $request): View
    {
        $filter = FilterDTO::fromRequest($request);
        $incidents = $this->service->getPaginated($filter);
        $stats = $this->service->getStatistics();

        return view('incidents.index', compact('incidents', 'stats', 'filter'));
    }

    /**
     * Show the form for creating a new incident
     */
    public function create(): View
    {
        return view('incidents.create');
    }

    /**
     * Store a newly created incident in storage
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $dto = IncidentDTO::fromArray(
            array_merge($request->validated(), ['reported_by' => auth()->id()])
        );

        try {
            $incident = $this->service->create($dto);
            $this->logCreated($incident, $request);

            return redirect()->route('incidents.show', $incident->id)
                ->with('success', 'Incident berhasil dibuat');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat incident: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified incident
     */
    public function show(Incident $incident): View
    {
        $this->logViewed($incident, request());

        $detail = $this->service->getWithRelations($incident->id);
        $navigation = $this->service->getNavigation($incident->id);
        $auditLogs = $this->auditService->getForIncident($incident->id);

        return view('incidents.show', [
            'incident' => $detail,
            'previousIncident' => $navigation['previous'] ?? null,
            'nextIncident' => $navigation['next'] ?? null,
            'auditLogs' => $auditLogs,
        ]);
    }

    /**
     * Show the form for editing the specified incident
     */
    public function edit(Incident $incident): View
    {
        return view('incidents.edit', compact('incident'));
    }

    /**
     * Update the specified incident in storage
     */
    public function update(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        $oldValues = $incident->toArray();
        $dto = IncidentDTO::fromArray($request->validated());

        try {
            $this->service->update($incident, $dto);
            $this->logUpdated($incident, $oldValues, $request);

            return redirect()->route('incidents.show', $incident->id)
                ->with('success', 'Incident berhasil diupdate');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mengupdate incident: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete the specified incident
     */
    public function destroy(Request $request, Incident $incident): RedirectResponse
    {
        try {
            $this->service->delete($incident);
            $this->logDeleted($incident, $request);

            return redirect()->route('incidents.index')
                ->with('success', 'Incident berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus incident: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted incident
     */
    public function restore(Request $request, int $id): RedirectResponse
    {
        try {
            $this->service->restore($id);
            $this->logRestored(Incident::withTrashed()->findOrFail($id), $request);

            return redirect()->route('incidents.show', $id)
                ->with('success', 'Incident berhasil di-restore');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal restore incident: ' . $e->getMessage());
        }
    }

    /**
     * Export incidents as CSV
     */
    public function export(Request $request)
    {
        $filter = FilterDTO::fromRequest($request);
        $exportable = $this->service->getExportable($filter);

        $filename = 'incidents_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($exportable) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['ID', 'Title', 'Severity', 'Status', 'Reported By', 'Incident Date', 'Created At']);

            foreach ($exportable as $incident) {
                $formatted = $this->service->formatForExport($incident);
                fputcsv($file, [
                    $formatted['id'],
                    $formatted['title'],
                    $formatted['severity'],
                    $formatted['status'],
                    $formatted['reported_by'],
                    $formatted['incident_date'],
                    $formatted['created_at'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
