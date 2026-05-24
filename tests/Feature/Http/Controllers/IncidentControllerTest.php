<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_incidents_list()
    {
        Incident::factory(5)->create();

        $response = $this->actingAs($this->user)
            ->get(route('incidents.index'));

        $response->assertStatus(200);
        $response->assertViewHas('incidents');
    }

    public function test_can_filter_incidents_by_severity()
    {
        Incident::factory(3)->create(['severity' => 'Critical']);
        Incident::factory(2)->create(['severity' => 'Low']);

        $response = $this->actingAs($this->user)
            ->get(route('incidents.index', ['severity' => 'Critical']));

        $response->assertStatus(200);
        $incidents = $response->viewData('incidents');
        $this->assertEquals(3, $incidents->total());
    }

    public function test_can_search_incidents()
    {
        Incident::factory()->create(['title' => 'Database Connection']);
        Incident::factory()->create(['title' => 'API Timeout']);

        $response = $this->actingAs($this->user)
            ->get(route('incidents.index', ['search' => 'Database']));

        $response->assertStatus(200);
        $incidents = $response->viewData('incidents');
        $this->assertEquals(1, $incidents->total());
    }

    public function test_can_view_create_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('incidents.create'));

        $response->assertStatus(200);
        $response->assertViewIs('incidents.create');
    }

    public function test_can_create_incident()
    {
        $data = [
            'title' => 'New Incident',
            'description' => 'Test description for incident',
            'severity' => 'High',
            'status' => 'Open',
            'incident_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)
            ->post(route('incidents.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('incidents', ['title' => 'New Incident']);
    }

    public function test_create_requires_authentication()
    {
        $response = $this->get(route('incidents.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_can_view_incident_detail()
    {
        $incident = Incident::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('incidents.show', $incident));

        $response->assertStatus(200);
        $response->assertViewHas('detail');
    }

    public function test_can_update_incident()
    {
        $incident = Incident::factory()->create();
        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated description for incident',
            'severity' => 'Critical',
            'status' => 'On Progress',
            'incident_date' => $incident->incident_date->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)
            ->put(route('incidents.update', $incident), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('incidents', ['title' => 'Updated Title']);
    }

    public function test_can_delete_incident()
    {
        $incident = Incident::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('incidents.destroy', $incident));

        $response->assertRedirect();
        $this->assertSoftDeleted('incidents', ['id' => $incident->id]);
    }
}
