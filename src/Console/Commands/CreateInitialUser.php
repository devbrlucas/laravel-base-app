<?php

declare(strict_types=1);

namespace DevBRLucas\Console\Commands;

use Illuminate\Console\Command;

class CreateInitialUser extends Command
{
    protected $signature = 'devbrlucas:init-user {model}';

    protected $description = 'Create the initial user in the database';

    public function handle(): int
    {
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
        $this->argument('model')::query()->create($data);
        $this->info('User created successfully');
        exit(0);
    }
}
