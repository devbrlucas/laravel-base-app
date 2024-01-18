<?php

declare(strict_types=1);

namespace DevBRLucas\LaravelBaseApp\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateInitialUser extends Command
{
    protected $signature = 'devbrlucas:init-user {model}';

    protected $description = 'Create the initial user in the database';

    public function handle(): int
    {
        DB::transaction(function(): void {
            $this->warn('Creating initial user');
            $data = [];
            $fields = config('laravel-base-app.initial_user_fields');
            foreach ($fields as $field) {
                $data[$field] = $this->ask("Enter the user's $field");
            }
            do {
                $password = $this->secret('Enter the user\'s password');
                $passwordConfirmation = $this->secret('Confirm the user\'s password');
                if ($password !== $passwordConfirmation) $this->error('Passwords do not match');
            } while ($password !== $passwordConfirmation);
            $data['password'] = $password;
            $user = $this->argument('model')::query()->create($data);
            $callback = config('laravel-base-app.initial_user_callback');
            if ($callback) $callback($user);
            $this->info('User created successfully');
        });
        exit(0);
    }
}
