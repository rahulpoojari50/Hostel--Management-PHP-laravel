<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MealsTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_meals_index_page_loads()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a meal
        $meal = Meal::create([
            'hostel_id' => $hostel->id,
            'meal_type' => 'breakfast',
            'meal_date' => now(),
            'menu_description' => 'Test menu'
        ]);

        // Act as the warden and visit the meals index page
        $response = $this->actingAs($warden)
            ->get('/warden/meals');

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee('Meals Management');
        $response->assertSee('Add New Meal');
        $response->assertSee($hostel->name);
        $response->assertSee('Breakfast');
    }

    public function test_meals_create_page_loads()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Act as the warden and visit the meals create page
        $response = $this->actingAs($warden)
            ->get('/warden/meals/create');

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee('Add New Meal');
        $response->assertSee($hostel->name);
        $response->assertSee('Meal Type');
        $response->assertSee('Create Meal');
    }

    public function test_meals_show_page_loads()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a student user
        $student = User::factory()->create([
            'role' => 'student'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a meal
        $meal = Meal::create([
            'hostel_id' => $hostel->id,
            'meal_type' => 'lunch',
            'meal_date' => now(),
            'menu_description' => 'Test menu description'
        ]);

        // Act as the warden and visit the meals show page
        $response = $this->actingAs($warden)
            ->get('/warden/meals/' . $meal->id);

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee('Meal Details');
        $response->assertSee($hostel->name);
        $response->assertSee('Lunch');
        $response->assertSee('Attendance Management');
    }

    public function test_meals_edit_page_loads()
    {
        // Create a warden user
        $warden = User::factory()->create([
            'role' => 'warden'
        ]);

        // Create a hostel
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'type' => 'boys',
            'address' => 'Test Address',
            'warden_id' => $warden->id,
            'status' => 'active'
        ]);

        // Create a meal
        $meal = Meal::create([
            'hostel_id' => $hostel->id,
            'meal_type' => 'dinner',
            'meal_date' => now(),
            'menu_description' => 'Test menu'
        ]);

        // Act as the warden and visit the meals edit page
        $response = $this->actingAs($warden)
            ->get('/warden/meals/' . $meal->id . '/edit');

        // Assert the page loads successfully
        $response->assertStatus(200);
        $response->assertSee('Edit Meal');
        $response->assertSee($hostel->name);
        $response->assertSee('Dinner');
        $response->assertSee('Update Meal');
    }
} 