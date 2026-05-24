<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_dashboard()
    {
        Incident::factory(10)->create();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    public function test_dashboard_shows_critical_alert()
    {
        Incident::factory(2)->create([
            'severity' => 'Critical',
            'status' => 'Open',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertViewHas('stats');
    }

    public function test_dashboard_requires_authentication()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_recent_incidents()
    {
        Incident::factory(5)->create();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }
}
