<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('cms.dashboard'));
    $response->assertRedirect(route('login'));
});

test('admins can visit the dashboard', function () {
    $this->seed(PermissionSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    $response = $this->get(route('cms.dashboard'));
    $response->assertStatus(200);
});

test('regular users cannot visit the dashboard', function () {
    $this->seed(PermissionSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('user');

    $this->actingAs($user);

    $response = $this->get(route('cms.dashboard'));
    $response->assertForbidden();
});
