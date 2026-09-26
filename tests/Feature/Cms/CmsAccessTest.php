<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);

    $this->seed(PermissionSeeder::class);
});

it('prevents an admin from granting the superadmin role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $target = User::factory()->create();
    $target->assignRole('user');

    $this->actingAs($admin)
        ->from(route('cms.management.users.edit', $target))
        ->put(route('cms.management.users.update', $target), [
            'role' => 'superadmin',
            'name' => $target->name,
            'email' => $target->email,
            'phone' => $target->phone,
        ])
        ->assertSessionHasErrors('role');

    expect($target->fresh()->hasRole('superadmin'))->toBeFalse();
});

it('allows a superadmin to grant the superadmin role', function () {
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    $target = User::factory()->create();
    $target->assignRole('user');

    $this->actingAs($superadmin)
        ->from(route('cms.management.users.edit', $target))
        ->put(route('cms.management.users.update', $target), [
            'role' => 'superadmin',
            'name' => $target->name,
            'email' => $target->email,
            'phone' => $target->phone,
        ])
        ->assertSessionHasNoErrors();

    expect($target->fresh()->hasRole('superadmin'))->toBeTrue();
});
