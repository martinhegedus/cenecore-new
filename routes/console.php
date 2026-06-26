<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-admin', function () {
    $name = $this->ask('Name');
    $email = $this->ask('Email');
    $password = $this->secret('Password');

    Validator::make([
        'name' => $name,
        'email' => $email,
        'password' => $password,
    ], [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        'password' => ['required', Rules\Password::defaults()],
    ])->validate();

    $user = User::query()->where('email', $email)->first();

    if ($user) {
        $user->forceFill([
            'name' => $name,
            'password' => $password,
            'is_admin' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $this->info("Admin access granted to {$user->email}.");

        return;
    }

    $user = new User;
    $user->forceFill([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'is_admin' => true,
        'email_verified_at' => now(),
    ])->save();

    $this->info("Admin user {$user->email} created.");
})->purpose('Create or promote a user with Filament admin access');
