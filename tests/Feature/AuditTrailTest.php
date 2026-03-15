<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_creating_a_model_logs_audit()
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->user->id,
            'event' => 'created',
            'auditable_type' => Customer::class,
            'auditable_id' => $customer->id,
        ]);

        $log = AuditLog::where('auditable_type', Customer::class)
            ->where('auditable_id', $customer->id)
            ->first();
        $this->assertNotNull($log->new_values);
        $this->assertEquals('John Doe', $log->new_values['name']);
    }

    public function test_updating_a_model_logs_audit()
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '1234567890',
            'email' => 'john@example.com',
        ]);

        $customer->update(['name' => 'Jane Doe']);

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'updated',
            'auditable_id' => $customer->id,
        ]);

        $log = AuditLog::where('event', 'updated')->first();
        $this->assertEquals('John Doe', $log->old_values['name']);
        $this->assertEquals('Jane Doe', $log->new_values['name']);
    }

    public function test_deleting_a_model_logs_audit()
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'phone' => '1234567890',
            'email' => 'john@example.com',
        ]);

        $customer->delete();

        $this->assertDatabaseHas('audit_logs', [
            'event' => 'deleted',
            'auditable_id' => $customer->id,
        ]);

        $log = AuditLog::where('event', 'deleted')->first();
        $this->assertEquals('John Doe', $log->old_values['name']);
    }
}
