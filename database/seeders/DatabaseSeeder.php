<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Incident;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin users
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@greenalert.local',
            'password' => 'admin123',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create operator users
        $operator1 = User::create([
            'name' => 'Operator One',
            'email' => 'operator1@greenalert.local',
            'password' => 'operator123',
            'role' => 'operator',
            'email_verified_at' => now(),
        ]);

        $operator2 = User::create([
            'name' => 'Operator Two',
            'email' => 'operator2@greenalert.local',
            'password' => 'operator123',
            'role' => 'operator',
            'email_verified_at' => now(),
        ]);

        $operator3 = User::create([
            'name' => 'Operator Three',
            'email' => 'operator3@greenalert.local',
            'password' => 'operator123',
            'role' => 'operator',
            'email_verified_at' => now(),
        ]);

        // Create sample incidents with different severities
        $incidentData = [
            // Critical incidents
            [
                'title' => 'Database Server Down - Critical',
                'description' => 'Primary database server is completely down and not responding to any requests. All database queries are failing. This is affecting all production systems and applications.',
                'severity' => 'Critical',
                'status' => 'Open',
                'reported_by' => $admin->id,
                'incident_date' => now()->subDays(2),
            ],
            [
                'title' => 'Network Outage in Datacenter',
                'description' => 'Major network outage affecting the entire datacenter. All services are unreachable. Network team is investigating the root cause.',
                'severity' => 'Critical',
                'status' => 'On Progress',
                'reported_by' => $operator1->id,
                'incident_date' => now()->subHours(6),
            ],
            [
                'title' => 'Security Breach Detected',
                'description' => 'Suspicious activity detected on production servers. Unauthorized access attempts have been logged. Security team is investigating and implementing containment measures.',
                'severity' => 'Critical',
                'status' => 'On Progress',
                'reported_by' => $admin->id,
                'incident_date' => now()->subHours(4),
            ],

            // High severity incidents
            [
                'title' => 'Application Server Memory Leak',
                'description' => 'Application server is experiencing continuous memory usage increase leading to performance degradation. Server needs to be restarted.',
                'severity' => 'High',
                'status' => 'Open',
                'reported_by' => $operator2->id,
                'incident_date' => now()->subDays(1),
            ],
            [
                'title' => 'API Response Time Degradation',
                'description' => 'API responses are taking 5-10 seconds on average, normal response time is 500ms. Users are experiencing significant delays.',
                'severity' => 'High',
                'status' => 'On Progress',
                'reported_by' => $operator1->id,
                'incident_date' => now()->subHours(8),
            ],
            [
                'title' => 'SSL Certificate Expiration Warning',
                'description' => 'SSL certificate for production domain will expire in 3 days. We need to renew immediately to prevent service interruption.',
                'severity' => 'High',
                'status' => 'On Progress',
                'reported_by' => $admin->id,
                'incident_date' => now()->subDays(3),
            ],

            // Medium severity incidents
            [
                'title' => 'Email Service Intermittent Issues',
                'description' => 'Email service is experiencing intermittent failures. Some emails are not being delivered. Impact is limited to a subset of users.',
                'severity' => 'Medium',
                'status' => 'Open',
                'reported_by' => $operator3->id,
                'incident_date' => now()->subDays(5),
            ],
            [
                'title' => 'Backup Process Failed',
                'description' => 'Last night backup job failed to complete. Need to investigate and ensure backup integrity. Manual backup has been initiated.',
                'severity' => 'Medium',
                'status' => 'Resolved',
                'reported_by' => $operator2->id,
                'incident_date' => now()->subDays(7),
            ],
            [
                'title' => 'Storage Capacity Warning',
                'description' => 'Server storage is at 85% capacity. Need to perform cleanup and consider capacity expansion planning.',
                'severity' => 'Medium',
                'status' => 'Open',
                'reported_by' => $admin->id,
                'incident_date' => now()->subDays(4),
            ],

            // Low severity incidents
            [
                'title' => 'Documentation Update Required',
                'description' => 'API documentation is outdated and needs to be updated with latest endpoints and parameters.',
                'severity' => 'Low',
                'status' => 'Open',
                'reported_by' => $operator1->id,
                'incident_date' => now()->subDays(10),
            ],
            [
                'title' => 'Minor UI Layout Issue',
                'description' => 'Dashboard layout is slightly misaligned on mobile devices. Does not affect functionality but impacts user experience.',
                'severity' => 'Low',
                'status' => 'Resolved',
                'reported_by' => $operator3->id,
                'incident_date' => now()->subDays(15),
            ],
            [
                'title' => 'Font Rendering Issue on Reports',
                'description' => 'Some reports are showing unusual font rendering in Firefox browser. Works fine on Chrome.',
                'severity' => 'Low',
                'status' => 'Open',
                'reported_by' => $operator2->id,
                'incident_date' => now()->subDays(8),
            ],
        ];

        foreach ($incidentData as $data) {
            Incident::create($data);
        }

        // Create additional random incidents (menambah hingga total ~35)
        Incident::factory(23)->create([
            'reported_by' => User::inRandomOrder()->first()->id,
        ]);

        // Create some audit logs for tracking
        $incidents = Incident::all();
        foreach ($incidents->random(5) as $incident) {
            AuditLog::create([
                'user_id' => User::inRandomOrder()->first()->id,
                'incident_id' => $incident->id,
                'action' => 'view',
                'ip_address' => '192.168.' . rand(0, 255) . '.' . rand(0, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now(),
            ]);
        }

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('Admin account: admin@greenalert.local / admin123');
        $this->command->info('Operator accounts: operator1/2/3@greenalert.local / operator123');
    }
}

