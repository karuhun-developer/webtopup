<?php

use App\Actions\Cms\Management\User\DeleteUserAction;
use App\Actions\Cms\Management\User\StoreUserAction;
use App\Actions\Cms\Management\User\UpdateUserAction;
use App\Actions\Cms\Management\User\UpdateUserPasswordAction;
use App\Models\Spatie\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('store user action creates a user with role', function () {
    $role = Role::create(['name' => 'Test Role', 'guard_name' => 'api']);
    $action = new StoreUserAction;
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'role' => 'Test Role',
    ];

    $user = $action->handle($data);

    expect($user)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    expect($user->hasRole('Test Role'))->toBeTrue();
});

test('store user action creates a user without role', function () {
    $action = new StoreUserAction;
    $data = [
        'name' => 'Test User No Role',
        'email' => 'norole@example.com',
        'password' => 'password',
    ];

    $user = $action->handle($data);

    expect($user)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', ['email' => 'norole@example.com']);
    expect($user->roles()->count())->toBe(0);
});

test('update user action updates user details and role', function () {
    $roleOld = Role::create(['name' => 'Old Role', 'guard_name' => 'api']);
    $roleNew = Role::create(['name' => 'New Role', 'guard_name' => 'api']);

    $user = User::factory()->create();
    $user->assignRole('Old Role');

    $action = new UpdateUserAction;
    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'role' => 'New Role',
    ];

    $result = $action->handle($user, $data);

    expect($result)->toBeTrue();
    $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    expect($user->refresh()->hasRole('New Role'))->toBeTrue();
    expect($user->hasRole('Old Role'))->toBeFalse();
});

test('delete user action deletes a user', function () {
    $user = User::factory()->create();
    $action = new DeleteUserAction;

    $result = $action->handle($user);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('update user password action updates password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $action = new UpdateUserPasswordAction;
    $data = ['password' => 'new-password'];

    $result = $action->handle($user, $data);

    expect($result)->toBeTrue();
    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});
