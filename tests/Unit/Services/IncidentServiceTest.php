<?php

namespace Tests\Unit\Services;

use App\Services\IncidentService;
use App\Repositories\IncidentRepository;
use App\DTOs\IncidentDTO;
use App\DTOs\FilterDTO;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentServiceTest extends TestCase
{
    use RefreshDatabase;

    private IncidentService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(IncidentService::class);
        $this->user = User::factory()->create();
    }

    public function test_can_create_incident_from_dto()
    {
        $dto = new IncidentDTO(
            title: 'Critical Bug',
            description: 'Production bug affecting users',
            severity: 'Critical',
            status: 'Open',
            incident_date: now(),
            reported_by: $this->user->id,
        );

        $incident = $this->service->create($dto);

        $this->assertInstanceOf(Incident::class, $incident);
        $this->assertEquals('Critical Bug', $incident->title);
        $this->assertDatabaseHas('incidents', ['title' => 'Critical Bug']);
    }

    public function test_can_get_paginated_incidents()
    {
        Incident::factory(15)->create();
        $filter = new FilterDTO();

        $paginated = $this->service->getPaginated($filter);

        $this->assertEquals(10, count($paginated->items()));
    }

    public function test_can_get_statistics()
    {
        Incident::factory(5)->create();
        Incident::factory(2)->create(['severity' => 'Critical']);

        $stats = $this->service->getStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('critical', $stats);
    }

    public function test_can_filter_by_severity()
    {
        Incident::factory(3)->create(['severity' => 'Critical']);
        Incident::factory(2)->create(['severity' => 'Low']);

        $filter = new FilterDTO(severity: 'Critical');
        $paginated = $this->service->getPaginated($filter);

        $this->assertEquals(3, $paginated->total());
    }

    public function test_can_update_incident()
    {
        $incident = Incident::factory()->create(['reported_by' => $this->user->id]);
        $dto = new IncidentDTO(
            title: 'Updated Title',
            description: $incident->description,
            severity: 'High',
            status: 'On Progress',
            incident_date: $incident->incident_date,
            reported_by: $incident->reported_by,
            id: $incident->id,
        );

        $this->service->update($incident, $dto);

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_can_delete_incident()
    {
        $incident = Incident::factory()->create();

        $result = $this->service->delete($incident);

        $this->assertTrue($result);
        $this->assertSoftDeleted('incidents', ['id' => $incident->id]);
    }

    public function test_can_export_formatted()
    {
        $incident = Incident::factory()->create();

        $formatted = $this->service->formatForExport($incident);

        $this->assertIsArray($formatted);
        $this->assertArrayHasKey('id', $formatted);
        $this->assertArrayHasKey('title', $formatted);
        $this->assertArrayHasKey('severity', $formatted);
    }
}
